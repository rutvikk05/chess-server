<?php

namespace ChessServer;

use Chess\Board;
use Chess\Game;
use Chess\Movetext;
use ChessServer\Command\AcceptPlayRequestCommand;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\OnlineGamesCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\QuitCommand;
use ChessServer\Command\RematchCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\RestartCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\TakebackCommand;
use ChessServer\Command\UndoMoveCommand;
use ChessServer\Exception\ParserException;
use ChessServer\GameMode\AbstractMode;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\GrandmasterMode;
use ChessServer\GameMode\LoadFenMode;
use ChessServer\GameMode\LoadPgnMode;
use ChessServer\GameMode\PlayMode;
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

        if (is_a($cmd, AcceptPlayRequestCommand::class)) {
            if ($gameMode = $this->findGameMode($this->parser->argv[1])) {
                if ($this->syncGameModeWith($gameMode, $from)) {
                    $jwt = $gameMode->getJwt();
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
                    'mode' => PlayMode::NAME,
                    'message' =>  'This friend request could not be accepted.',
                ],
            ]);
        } elseif (is_a($cmd, DrawCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, OnlineGamesCommand::class)) {
            return $this->sendToOne($from->resourceId, [
                $cmd->name => $this->findOnlineGames(),
            ]);
        } elseif (is_a($cmd, PlayFenCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
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
        } elseif (is_a($cmd, RematchCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, ResignCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, RestartCommand::class)) {
            if ($gameMode = $this->findGameMode($this->parser->argv[1])) {
                $jwt = $gameMode->getJwt();
                $decoded = JWT::decode($jwt, $_ENV['JWT_SECRET'], array('HS256'));
                $decoded->iat = time();
                $decoded->exp = time() + 3600; // one hour by default
                $newJwt = JWT::encode($decoded, $_ENV['JWT_SECRET']);
                $resourceIds = $gameMode->getResourceIds();
                $newGameMode = new PlayMode(
                    new Game(Game::MODE_PLAY),
                    [$resourceIds[0], $resourceIds[1]],
                    $newJwt
                );
                $this->gameModes[$resourceIds[0]] = $newGameMode;
                $this->gameModes[$resourceIds[1]] = $newGameMode;
                return $this->sendToMany($newGameMode->getResourceIds(), [
                    $cmd->name => [
                        'jwt' => $newJwt,
                        'hash' => md5($newJwt),
                    ],
                ]);
            }
        } elseif (is_a($cmd, StartCommand::class)) {
            if ($gameMode) {
                return $this->sendToOne($from->resourceId, [
                    $cmd->name => 'Game already started.',
                ]);
            }
            if (AnalysisMode::NAME === $this->parser->argv[1]) {
                $this->gameModes[$from->resourceId] = new AnalysisMode(
                    new Game(Game::MODE_ANALYSIS),
                    [$from->resourceId]
                );
                $res = [
                    $cmd->name => [
                        'mode' => AnalysisMode::NAME,
                    ],
                ];
            } elseif (GrandmasterMode::NAME === $this->parser->argv[1]) {
                $this->gameModes[$from->resourceId] = new GrandmasterMode(
                    new Game(Game::MODE_GRANDMASTER),
                    [$from->resourceId]
                );
                $res = [
                    $cmd->name => [
                        'mode' => GrandmasterMode::NAME,
                        'color' => $this->parser->argv[2],
                    ],
                ];
            } elseif (LoadFenMode::NAME === $this->parser->argv[1]) {
                try {
                    $fenMode = new LoadFenMode(
                        new Game(Game::MODE_LOAD_FEN),
                        [$from->resourceId],
                        $this->parser->argv[2]
                    );
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
            } elseif (LoadPgnMode::NAME === $this->parser->argv[1]) {
                try {
                    $movetext = (new Movetext($this->parser->argv[2]))->validate();
                    $pgnMode = new LoadPgnMode(
                        new Game(Game::MODE_LOAD_PGN),
                        [$from->resourceId]
                    );
                    $game = $pgnMode->getGame();
                    $game->loadPgn($movetext);
                    $pgnMode->setGame($game);
                    $this->gameModes[$from->resourceId] = $pgnMode;
                    $board = new Board();
                    $history = [
                        array_values($board->toAsciiArray()),
                    ];
                    $moves = explode(' ', $movetext);
                    foreach ($moves as $key => $move) {
                        if ($key % 2 === 0) {
                            $exploded = explode('.', $move);
                            $board->play('w', $exploded[1]);
                        } else {
                            $board->play('b', $move);
                        }
                        $history[] = array_values($board->toAsciiArray());
                    }
                    $res = [
                        $cmd->name => [
                            'mode' => LoadPgnMode::NAME,
                            'turn' => $game->state()->turn,
                            'movetext' => $movetext,
                            'fen' => $game->state()->fen,
                            'history' => $history
                        ],
                    ];
                } catch (\Throwable $e) {
                    $res = [
                        $cmd->name => [
                            'mode' => LoadPgnMode::NAME,
                            'message' => 'This PGN movetext could not be loaded.',
                        ],
                    ];
                }
            } elseif (PlayMode::NAME === $this->parser->argv[1]) {
                $settings = json_decode($this->parser->argv[2]);
                $payload = [
                    'iss' => $_ENV['JWT_ISS'],
                    'iat' => time(),
                    'color' => $settings->color,
                    'min' => $settings->min,
                    'increment' => $settings->increment,
                    'submode' => $settings->submode,
                    'exp' => time() + 3600 // one hour by default
                ];
                $jwt = JWT::encode($payload, $_ENV['JWT_SECRET']);
                $this->gameModes[$from->resourceId] = new PlayMode(
                    new Game(Game::MODE_PLAY),
                    [$from->resourceId],
                    $jwt
                );
                $res = [
                    $cmd->name => [
                        'mode' => PlayMode::NAME,
                        'jwt' => $jwt,
                        'hash' => md5($jwt),
                    ],
                ];
            }
            return $this->sendToOne($from->resourceId, $res);
        } elseif (is_a($cmd, TakebackCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, UndoMoveCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
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
        if(isset($this->clients[$conn->resourceId])) {
            unset($this->clients[$conn->resourceId]);
        }

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

    protected function findOnlineGames()
    {
        $onlineGames = [];
        foreach ($this->gameModes as $gameMode) {
          if (is_a($gameMode, PlayMode::class)) {
            $jwt = $gameMode->getJwt();
            $onlineGames[] = JWT::decode($jwt, $_ENV['JWT_SECRET'], array('HS256'));
          }
        }

        return $onlineGames;
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
