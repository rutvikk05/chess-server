<?php

namespace ChessServer\GameMode;

use Chess\Game;
use ChessServer\Command\HeuristicsCommand;
use ChessServer\Command\PieceCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\ResponseCommand;
use ChessServer\Command\StatusCommand;
use ChessServer\Command\UndoMoveCommand;

abstract class AbstractMode
{
    protected $game;

    protected $resourceIds;

    protected $hash;

    public function __construct(Game $game, array $resourceIds)
    {
        $this->game = $game;
        $this->resourceIds = $resourceIds;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    public function getResourceIds(): array
    {
        return $this->resourceIds;
    }

    public function setResourceIds(array $resourceIds)
    {
        $this->resourceIds = $resourceIds;

        return $this;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function res($argv, $cmd)
    {
        try {
            switch (get_class($cmd)) {
                case HeuristicsCommand::class:
                    return [
                        $cmd->name => [
                            'dimensions' => (new \Chess\Heuristics(''))->getDimensions(),
                            'balance' => $this->game->heuristics(true),
                        ],
                    ];
                case PieceCommand::class:
                    return [
                        $cmd->name => $this->game->piece($argv[1]),
                    ];
                case PlayFenCommand::class:
                    return [
                        $cmd->name => [
                            'turn' => $this->game->status()->turn,
                            'legal' => $this->game->playFen($argv[1]),
                            'check' => $this->game->isCheck(),
                            'mate' => $this->game->isMate(),
                            'movetext' => $this->game->movetext(),
                            'fen' => $this->game->fen(),
                        ],
                    ];
                case ResponseCommand::class:
                    $response = $this->game->response();
                    if ($response) {
                        $this->game->play($this->game->status()->turn, $response);
                        return [
                            $cmd->name => [
                                'turn' => $this->game->status()->turn,
                                'check' => $this->game->isCheck(),
                                'mate' => $this->game->isMate(),
                                'movetext' => $this->game->movetext(),
                                'fen' => $this->game->fen(),
                            ],
                        ];
                    }
                    return [
                        $cmd->name => null,
                    ];
                case StatusCommand::class:
                    return [
                        $cmd->name => $this->game->status(),
                    ];
                case UndoMoveCommand::class:
                    return [
                        $cmd->name => $this->game->undoMove(),
                    ];
                default:
                    return null;
            }
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
