<?php

namespace ChessServer;

use Chess\Game;
use Chess\PGN\Symbol;
use ChessServer\Command\AcceptFriendRequestCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\QuitCommand;
use ChessServer\Command\TakebackCommand;
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

        $this->parser = new CommandParser;

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

        $this->log->info('New connection', ['id' => $conn->resourceId, 'n' => count($this->clients)]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            return $this->sendToOne($from->resourceId, [
                'validate' => $e->getMessage(),
            ]);
        }

        $gameMode = $this->gameModes[$from->resourceId] ?? null;

        if (is_a($cmd, AcceptFriendRequestCommand::class)) {
            if ($gameMode = $this->findGameMode($this->parser->argv[1])) {
                if ($this->syncGameModeWith($gameMode, $from)) {
                    $jwt = $gameMode->getJwt();
                    $decoded = JWT::decode($jwt, $_ENV['JWT_SECRET'], array('HS256'));
                    return $this->sendToMany($gameMode->getResourceIds(), [
                        $cmd->name => [
                            'jwt' => $jwt,
                            'hash' => md5($jwt),
                        ],
                    ]);
                }
            }
            return $this->sendToOne($from->resourceId, [
                $cmd->name => [
                    'mode' => PlayFriendMode::NAME,
                    'message' =>  'This friend request could not be accepted.',
                ],
            ]);
        } elseif (is_a($cmd, QuitCommand::class)) {
            if ($gameMode) {
                unset($this->gameModes[$from->resourceId]);
                return $this->sendToOne($from->resourceId, [
                    $cmd->name => 'Good bye!',
                ]);
            }
            return $this->sendToOne($from->resourceId, [
                $cmd->name => 'A game needs to be started first for this command to be allowed.',
            ]);
        } elseif (is_a($cmd, StartCommand::class)) {
            if ($gameMode) {
                return $this->sendToOne($from->resourceId, [
                    $cmd->name => 'Game already started.',
                ]);
            }
            if (AnalysisMode::NAME === $this->parser->argv[1]) {
                $this->gameModes[$from->resourceId] = new AnalysisMode(new Game, [$from->resourceId]);
                $res = [
                    $cmd->name => [
                        'mode' => AnalysisMode::NAME,
                    ],
                ];
            } elseif (LoadFenMode::NAME === $this->parser->argv[1]) {
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
            } elseif (PlayFriendMode::NAME === $this->parser->argv[1]) {
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
            }
            return $this->sendToOne($from->resourceId, $res);
        } elseif (is_a($cmd, PlayFenCommand::class)) {
            if (is_a($gameMode, PlayFriendMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            } elseif ($gameMode) {
                return $this->sendToOne(
                    $from->resourceId,
                    $this->gameModes[$from->resourceId]->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, TakebackCommand::class)) {
            if (is_a($gameMode, PlayFriendMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif ($gameMode) {
            return $this->sendToOne(
                $from->resourceId,
                $this->gameModes[$from->resourceId]->res($this->parser->argv, $cmd)
            );
        }
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

    protected function findGameMode(string $hash)
    {
        foreach ($this->gameModes as $gameMode) {
            if ($hash === $gameMode->getHash()) {
                return $gameMode;
            }
        }

        return null;
    }

    protected function syncGameModeWith(AbstractMode $gameMode, ConnectionInterface $from)
    {
        if ($resourceIds = $gameMode->getResourceIds()) {
            if (count($resourceIds) === 1) {
                $resourceIds[] = $from->resourceId;
                $gameMode->setResourceIds($resourceIds);
                foreach ($resourceIds as $resourceId) {
                    $this->gameModes[$resourceId] = $gameMode;
                }
                return true;
            }
        }

        return false;
    }

    protected function sendToOne(int $resourceId, array $res)
    {
        $this->clients[$resourceId]->send(json_encode($res));

        $this->log->info('Sent message', [
            'id' => $resourceId,
            'res' => $res,
        ]);
    }

    protected function sendToMany(array $resourceIds, array $res)
    {
        foreach ($resourceIds as $resourceId) {
            $this->clients[$resourceId]->send(json_encode($res));
        }

        $this->log->info('Sent message', [
            'ids' => $resourceIds,
            'res' => $res,
        ]);
    }
}
