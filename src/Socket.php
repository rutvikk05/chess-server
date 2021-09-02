<?php

namespace ChessServer;

use Chess\Game;
use Chess\PGN\Symbol;
use ChessServer\Command\AcceptFriendRequest;
use ChessServer\Command\PlayFen;
use ChessServer\Command\Start;
use ChessServer\Command\Quit;
use ChessServer\Exception\ParserException;
use ChessServer\Mode\AbstractMode;
use ChessServer\Mode\AnalysisMode;
use ChessServer\Mode\LoadFenMode;
use ChessServer\Mode\PlayFriendMode;
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

    private $modes = [];

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

        isset($this->modes[$from->resourceId]) ? $mode = $this->modes[$from->resourceId] : $mode = null;

        if ($mode) {
            if (is_a($cmd, Quit::class)) {
                unset($this->modes[$from->resourceId]);
                $res = [
                    $cmd->name => 'Good bye!',
                ];
            } elseif (is_a($cmd, Start::class)) {
                $res = [
                    $cmd->name => 'Game already started.',
                ];
            } elseif (
                is_a($cmd, PlayFen::class) &&
                is_a($this->modes[$from->resourceId], PlayFriendMode::class)
            ) {
                $this->sendToMany(
                    $mode->getResourceIds(),
                    $mode->res($this->parser->argv, $cmd)
                );
                return;
            } else {
                $res = $this->modes[$from->resourceId]->res($this->parser->argv, $cmd);
            }
        } elseif (is_a($cmd, Start::class)) {
            switch ($this->parser->argv[1]) {
                case AnalysisMode::NAME:
                    $this->modes[$from->resourceId] = new AnalysisMode(new Game, [$from->resourceId]);
                    $res = [
                        $cmd->name => [
                            'mode' => AnalysisMode::NAME,
                        ],
                    ];
                    break;
                case LoadFenMode::NAME:
                    // TODO:
                    // See https://github.com/programarivm/php-chess/issues/36
                    // FEN validation logic should be written in programarivm/php-chess rather than
                    // catching a Throwable object in the chess server.
                    try {
                        $fenMode = new LoadFenMode(new Game, [$from->resourceId]);
                        $game = $fenMode->getGame();
                        $game->loadFen($this->parser->argv[2]);
                        $fenMode->setGame($game);
                        $this->modes[$from->resourceId] = $fenMode;
                        $res = [
                            $cmd->name => [
                                'mode' => LoadFenMode::NAME,
                                'fen' => $this->parser->argv[2],
                            ],
                        ];
                    } catch (\Throwable $e) {
                        $res = [
                            $cmd->name => 'Whoops! This FEN string could not be loaded.',
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
                    $this->modes[$from->resourceId] = new PlayFriendMode(new Game, [$from->resourceId], $jwt);
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
            if ($mode = $this->findMode($this->parser->argv[1])) {
                $this->syncModeWith($mode, $from);
                $jwt = $mode->getJwt();
                $decoded = JWT::decode($jwt, $_ENV['JWT_SECRET'], array('HS256'));
                $res = [
                    $cmd->name => [
                        'jwt' => $jwt,
                        'hash' => md5($jwt),
                    ],
                ];
                $this->sendToMany($mode->getResourceIds(), $res);
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

    protected function findMode(string $hash)
    {
        foreach ($this->modes as $mode) {
            if ($hash === $mode->getHash()) {
                return $mode;
            }
        }

        return null;
    }

    protected function syncModeWith(AbstractMode $mode, ConnectionInterface $from)
    {
        $resourceIds = $mode->getResourceIds();
        $resourceIds[] = $from->resourceId;
        $mode->setResourceIds($resourceIds);
        foreach ($resourceIds as $resourceId) {
            $this->modes[$resourceId] = $mode;
        }
    }

    protected function sendToMany(array $resourceIds, array $res)
    {
        foreach ($resourceIds as $resourceId) {
            $this->clients[$resourceId]->send(json_encode($res));
        }
    }
}
