<?php

namespace ChessServer;

use Chess\Game;
use Chess\Grandmaster;
use Chess\Movetext;
use Chess\Player;
use Chess\Variant\Classical\FEN\BoardToStr;
use Chess\Variant\Classical\FEN\StrToBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\Randomizer\Randomizer;
use Chess\Variant\Classical\Randomizer\Checkmate\TwoBishopsRandomizer;
use Chess\Variant\Classical\Randomizer\Endgame\PawnEndgameRandomizer;
use ChessServer\Command\AcceptPlayRequestCommand;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\LeaveCommand;
use ChessServer\Command\OnlineGamesCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\RandomizerCommand;
use ChessServer\Command\RematchCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\RestartCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\TakebackCommand;
use ChessServer\Command\UndoCommand;
use ChessServer\Exception\ParserException;
use ChessServer\GameMode\AbstractMode;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\GmMode;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\PgnMode;
use ChessServer\GameMode\PlayMode;
use ChessServer\GameMode\StockfishMode;
use ChessServer\Parser\CommandParser;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Socket implements MessageComponentInterface
{
    const DATA_FOLDER = __DIR__.'/../data';

    private $log;

    private $parser;

    private $gm;

    private $clients = [];

    private $gameModes = [];

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();

        $this->log = new Logger($_ENV['BASE_URL']);
        $this->log->pushHandler(new StreamHandler(__DIR__.'/../storage/pchess.log', Logger::INFO));

        $this->parser = new CommandParser;
        $this->gm = new Grandmaster(self::DATA_FOLDER.'/players.json');

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;

        $this->log->info('Started the chess server');
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;
        $this->log->info('New connection', [
            'id' => $conn->resourceId,
            'n' => count($this->clients)
        ]);
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
            if ($gameMode = $this->gameModeByHash($this->parser->argv[1])) {
                $gameMode->setState(PlayMode::STATE_ACCEPTED);
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
        } elseif (is_a($cmd, LeaveCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                $this->deleteGameModes($from->resourceId);
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, OnlineGamesCommand::class)) {
            return $this->sendToOne($from->resourceId, [
                $cmd->name => $this->playModesArrayByState(PlayMode::STATE_PENDING),
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
        } elseif (is_a($cmd, RandomizerCommand::class)) {
            try {
                $items = json_decode(stripslashes($this->parser->argv[2]), true);
                if (count($items) === 1) {
                    $color = array_key_first($items);
                    $ids = str_split(current($items));
                    if ($ids === ['B', 'B']) {
                        $board = (new TwoBishopsRandomizer($this->parser->argv[1]))->getBoard();
                    } elseif ($ids === ['P']) {
                        $board = (new PawnEndgameRandomizer($this->parser->argv[1]))->getBoard();
                    } else {
                        $board = (new Randomizer($this->parser->argv[1], [$color => $ids]))->getBoard();
                    }
                } else {
                    $wIds = str_split($items[Color::W]);
                    $bIds = str_split($items[Color::B]);
                    $board = (new Randomizer($this->parser->argv[1], [
                        Color::W => $wIds,
                        Color::B => $bIds,
                    ]))->getBoard();
                }
                $res = [
                    $cmd->name => [
                        'turn' => $board->getTurn(),
                        'fen' => (new BoardToStr($board))->create(),
                    ],
                ];
            } catch (\Throwable $e) {
                echo $e->getMessage();
                $res = [
                    $cmd->name => [
                        'message' => 'A random puzzle could not be loaded.',
                    ],
                ];
            }
            return $this->sendToOne($from->resourceId, $res);
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
            if ($gameMode = $this->gameModeByHash($this->parser->argv[1])) {
                $jwt = $gameMode->getJwt();
                $decoded = JWT::decode($jwt, $_ENV['JWT_SECRET'], array('HS256'));
                $decoded->iat = time();
                $decoded->exp = time() + 3600; // one hour by default
                $newJwt = JWT::encode($decoded, $_ENV['JWT_SECRET']);
                $resourceIds = $gameMode->getResourceIds();
                $newGameMode = new PlayMode(
                    new Game(Game::VARIANT_CLASSICAL, Game::MODE_PLAY),
                    [$resourceIds[0], $resourceIds[1]],
                    $newJwt
                );
                $newGameMode->setState(PlayMode::STATE_ACCEPTED);
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
            $variant = $this->parser->argv[1];
            $mode = $this->parser->argv[2];
            if (AnalysisMode::NAME === $mode) {
                $analysisMode = new AnalysisMode(
                    new Game($variant, $mode),
                    [$from->resourceId]
                );
                $this->gameModes[$from->resourceId] = $analysisMode;
                $res = [
                    $cmd->name => [
                        'variant' => $variant,
                        'mode' => $mode,
                        'fen' => $analysisMode->getGame()->getBoard()->toFen(),
                    ],
                ];
            } elseif (GmMode::NAME === $mode) {
                $this->gameModes[$from->resourceId] = new GmMode(
                    new Game($variant, $mode, $this->gm),
                    [$from->resourceId]
                );
                $res = [
                    $cmd->name => [
                        'variant' => $variant,
                        'mode' => $mode,
                        'color' => $this->parser->argv[3],
                    ],
                ];
            } elseif (FenMode::NAME === $mode) {
                try {
                    $fenMode = new FenMode(
                        new Game($variant, $mode),
                        [$from->resourceId],
                        $this->parser->argv[3]
                    );
                    $game = $fenMode->getGame();
                    $game->loadFen($this->parser->argv[3]);
                    $fenMode->setGame($game);
                    $this->gameModes[$from->resourceId] = $fenMode;
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'fen' => $this->parser->argv[3],
                        ],
                    ];
                } catch (\Throwable $e) {
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'message' => 'This FEN string could not be loaded.',
                        ],
                    ];
                }
            } elseif (PgnMode::NAME === $mode) {
                try {
                    $movetext = (new Movetext($this->parser->argv[3]))->validate();
                    $pgnMode = new PgnMode(
                        new Game($variant, $mode),
                        [$from->resourceId]
                    );
                    $player = (new Player($movetext))->play();
                    $game = $pgnMode->getGame()->setBoard($player->getBoard());
                    $pgnMode->setGame($game);
                    $this->gameModes[$from->resourceId] = $pgnMode;
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'turn' => $game->state()->turn,
                            'movetext' => $movetext,
                            'fen' => $game->state()->fen,
                            'history' => $player->getHistory(),
                        ],
                    ];
                } catch (\Throwable $e) {
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'message' => 'This PGN movetext could not be loaded.',
                        ],
                    ];
                }
            } elseif (PlayMode::NAME === $mode) {
                $settings = json_decode($this->parser->argv[3]);
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
                    new Game($variant, $mode),
                    [$from->resourceId],
                    $jwt
                );
                $res = [
                    $cmd->name => [
                        'variant' => $variant,
                        'mode' => $mode,
                        'jwt' => $jwt,
                        'hash' => md5($jwt),
                    ],
                ];
            } elseif (StockfishMode::NAME === $mode) {
                try {
                    $stockfishMode = new StockfishMode(
                        new Game($variant, $mode),
                        [$from->resourceId],
                        $this->parser->argv[3]
                    );
                    $game = $stockfishMode->getGame();
                    $game->loadFen($this->parser->argv[3]);
                    $stockfishMode->setGame($game);
                    $this->gameModes[$from->resourceId] = $stockfishMode;
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'color' => $game->getBoard()->getTurn(),
                            'fen' => $game->getBoard()->toFen(),
                        ],
                    ];
                } catch (\Throwable $e) {
                    if ($this->parser->argv[3] === Color::W || $this->parser->argv[3] === Color::B) {
                        $stockfishMode = new StockfishMode(
                            new Game($variant, $mode, $this->gm),
                            [$from->resourceId]
                        );
                        $this->gameModes[$from->resourceId] = $stockfishMode;
                        $res = [
                            $cmd->name => [
                                'variant' => $variant,
                                'mode' => $mode,
                                'color' => $this->parser->argv[3],
                            ],
                        ];
                    } else {
                        $res = [
                            $cmd->name => [
                                'variant' => $variant,
                                'mode' => $mode,
                                'message' => 'Stockfish could not be started.',
                            ],
                        ];
                    }
                }
            }
            return $this->sendToOne($from->resourceId, $res);
        } elseif (is_a($cmd, TakebackCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, UndoCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            } elseif (is_a($gameMode, GmMode::class)) {
                return $this->sendToOne(
                    $from->resourceId,
                    $gameMode->res($this->parser->argv, $cmd)
                );
            } elseif (is_a($gameMode, AnalysisMode::class)) {
                return $this->sendToOne(
                    $from->resourceId,
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
        $this->leaveGame($conn->resourceId);
        $this->deleteGameModes($conn->resourceId);
        $this->deleteClient($conn->resourceId);

        $this->log->info('Closed connection', ['id' => $conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();

        $this->log->info('Occurred an error', ['message' => $e->getMessage()]);
    }

    protected function gameModeByHash(string $hash)
    {
        foreach ($this->gameModes as $gameMode) {
            if ($hash === $gameMode->getHash()) {
                return $gameMode;
            }
        }

        return null;
    }

    protected function playModesArrayByState(string $state)
    {
        $result = [];
        foreach ($this->gameModes as $gameMode) {
          if (is_a($gameMode, PlayMode::class)) {
            if ($gameMode->getState() === $state) {
                $decoded = JWT::decode($gameMode->getJwt(), $_ENV['JWT_SECRET'], array('HS256'));
                if ($decoded->submode === PlayMode::SUBMODE_ONLINE) {
                    $decoded->hash = $gameMode->getHash();
                    $result[] = $decoded;
                }
            }
          }
        }

        return $result;
    }

    protected function gameModeByResourceId(int $id)
    {
        foreach ($this->gameModes as $key => $val) {
            if ($key === $id) {
                return $val;
            }
        }

        return null;
    }

    protected function leaveGame(int $resourceId)
    {
        if ($gameMode = $this->gameModeByResourceId($resourceId)) {
            $toId = null;
            $resourceIds = $gameMode->getResourceIds();
            if ($resourceIds[0] !== $resourceId) {
                $toId = $resourceIds[0];
            } elseif (isset($resourceIds[1]) && $resourceIds[1] !== $resourceId) {
                $toId = $resourceIds[1];
            }
            if ($toId) {
                $this->sendToOne($toId, ['/leave' => LeaveCommand::ACTION_ACCEPT]);
            }
        }
    }

    protected function deleteGameModes(int $resourceId)
    {
        if ($gameMode = $this->gameModeByResourceId($resourceId)) {
            $resourceIds = $gameMode->getResourceIds();
            if (isset($resourceIds[0])) {
                if (isset($this->gameModes[$resourceIds[0]])) {
                    unset($this->gameModes[$resourceIds[0]]);
                }
            }
            if (isset($resourceIds[1])) {
                if (isset($this->gameModes[$resourceIds[1]])) {
                    unset($this->gameModes[$resourceIds[1]]);
                }
            }
        }
    }

    protected function deleteClient(int $resourceId)
    {
        if (isset($this->clients[$resourceId])) {
            unset($this->clients[$resourceId]);
        }
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
        if (isset($this->clients[$resourceId])) {
            $this->clients[$resourceId]->send(json_encode($res));

            $this->log->info('Sent message', [
                'id' => $resourceId,
                'res' => $res,
            ]);
        }
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
