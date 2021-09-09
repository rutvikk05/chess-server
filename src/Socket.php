<?php

namespace ChessServer;

use Chess\Game;
use Chess\PGN\Symbol;
use ChessServer\Command\AcceptFriendRequest;
use ChessServer\Command\PlayFen;
use ChessServer\Command\Start;
use ChessServer\Command\Quit;
use ChessServer\Exception\ParserException;
use ChessServer\GameMode\AbstractMode;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\LoadFenMode;
use ChessServer\GameMode\PlayFriendMode;
use ChessServer\Parser\CommandParser;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Socket implements MessageComponentInterface
{
    private $clients = [];

    private $gameModes = [];

    private $parser;

    private $log;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();

        // create a command parser
        $this->parser = new CommandParser;

        // create a log channel
        $this->log = new Logger($_ENV['BASE_URL']);
        $this->log->pushHandler(new StreamHandler(__DIR__.'/../storage/pchess.log', Logger::INFO));

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;

        $this->log->info('Started the chess server');
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;

        $this->log->info('New connection', ['id' => $conn->resourceId]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            $res = [
                'validate' => $e->getMessage(),
            ];
            $this->clients[$from->resourceId]->send(json_encode($res));
            return;
        }

        isset($this->gameModes[$from->resourceId])
          ? $gameMode = $this->gameModes[$from->resourceId]
          : $gameMode = null;

        if ($gameMode) {
            if (is_a($cmd, Quit::class)) {
                unset($this->gameModes[$from->resourceId]);
                $res = [
                    $cmd->name => 'Good bye!',
                ];
            } elseif (is_a($cmd, Start::class)) {
                $res = [
                    $cmd->name => 'Game already started.',
                ];
            } elseif (
                is_a($cmd, PlayFen::class) &&
                is_a($this->gameModes[$from->resourceId], PlayFriendMode::class)
            ) {
                $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
                return;
            } else {
                $res = $this->gameModes[$from->resourceId]->res($this->parser->argv, $cmd);
            }
        } elseif (is_a($cmd, Start::class)) {
            switch ($this->parser->argv[1]) {
                case AnalysisMode::NAME:
                    $this->gameModes[$from->resourceId] = new AnalysisMode(new Game, [$from->resourceId]);
                    $res = [
                        $cmd->name => [
                            'mode' => AnalysisMode::NAME,
                        ],
                    ];
                    break;
                case LoadFenMode::NAME:
                    try {
                        $fenMode = new LoadFenMode(new Game, [$from->resourceId]);
                        $game = $fenMode->getGame();
                        $game->loadFen($this->parser->argv[2]);
                        $fenMode->setGame($game);
                        $this->gameModes[$from->resourceId] = $fenMode;
                        $res = [
                            $cmd->name => [
                                'mode' => LoadFenMode::NAME,
                                'fen' => $this->parser->argv[2],
                            ],
                        ];
                    } catch (\Throwable $e) {
                        $res = [
                            $cmd->name => [
                                'mode' => LoadFenMode::NAME,
                                'message' => 'This FEN string could not be loaded.',
                            ],
                        ];
                    }
                    break;
                case PlayFriendMode::NAME:
                    $payload = [
                        'iss' => $_ENV['JWT_ISS'],
                        'iat' => time(),
                        'color' => $this->parser->argv[2],
                        'min' => $this->parser->argv[3],
                        'exp' => time() + 600 // ten minutes by default
                    ];
                    $jwt = JWT::encode($payload, $_ENV['JWT_SECRET']);
                    $this->gameModes[$from->resourceId] = new PlayFriendMode(new Game, [$from->resourceId], $jwt);
                    $res = [
                        $cmd->name => [
                            'mode' => PlayFriendMode::NAME,
                            'jwt' => $jwt,
                            'hash' => md5($jwt),
                        ],
                    ];
                    break;
            }
        } elseif (in_array(Start::class, $cmd->dependsOn)) {
            $res = [
                $cmd->name => 'A game needs to be started first for this command to be allowed.',
            ];
        } elseif (is_a($cmd, AcceptFriendRequest::class)) {
            if ($gameMode = $this->findPlayFriendMode($this->parser->argv[1])) {
                $this->syncModeWith($gameMode, $from);
                $jwt = $gameMode->getJwt();
                $decoded = JWT::decode($jwt, $_ENV['JWT_SECRET'], array('HS256'));
                $res = [
                    $cmd->name => [
                        'jwt' => $jwt,
                        'hash' => md5($jwt),
                    ],
                ];
                $this->sendToMany($gameMode->getResourceIds(), $res);
                return;
            } else {
                $res = [
                    $cmd->name => "Friend request not found.",
                ];
            }
        }

        $this->clients[$from->resourceId]->send(json_encode($res));

        $this->log->info('Sent message', [
            'id' => $from->resourceId,
            'res' => $res,
        ]);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->log->info('Closed connection', ['id' => $conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();

        $this->log->info('Occurred an error', ['message' => $e->getMessage()]);
    }

    protected function findPlayFriendMode(string $hash)
    {
        foreach ($this->gameModes as $gameMode) {
            if ($hash === $gameMode->getHash()) {
                return $gameMode;
            }
        }

        return null;
    }

    protected function syncModeWith(AbstractMode $gameMode, ConnectionInterface $from)
    {
        $resourceIds = $gameMode->getResourceIds();
        $resourceIds[] = $from->resourceId;
        $gameMode->setResourceIds($resourceIds);
        foreach ($resourceIds as $resourceId) {
            $this->gameModes[$resourceId] = $gameMode;
        }
    }

    protected function sendToMany(array $resourceIds, array $res)
    {
        foreach ($resourceIds as $resourceId) {
            $this->clients[$resourceId]->send(json_encode($res));
        }
    }
}
