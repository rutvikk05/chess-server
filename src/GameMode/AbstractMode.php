<?php

namespace ChessServer\GameMode;

use Chess\Game;
use Chess\Heuristics;
use ChessServer\Command\HeuristicsCommand;
use ChessServer\Command\PieceCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\ResponseCommand;
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
                    $movetext = $this->game->getBoard()->getMovetext();
                    return [
                        $cmd->name => [
                            'dimensions' => (new Heuristics())->getDimensions(),
                            'balance' => (new Heuristics($movetext))->getBalance(),
                        ],
                    ];
                case PieceCommand::class:
                    return [
                        $cmd->name => $this->game->getBoard()->legalMoves($argv[1]),
                    ];
                case PlayFenCommand::class:
                    return [
                        $cmd->name => [
                            'turn' => $this->game->state()->turn,
                            'isLegal' => $this->game->playFen($argv[1]),
                            'isCheck' => $this->game->state()->isCheck,
                            'isMate' => $this->game->state()->isMate,
                            'movetext' => $this->game->state()->movetext,
                            'fen' => $this->game->state()->fen,
                        ],
                    ];
                case ResponseCommand::class:
                    $response = $this->game->response();
                    if ($response) {
                        $this->game->play($this->game->state()->turn, $response);
                        return [
                            $cmd->name => $this->game->state(),
                        ];
                    }
                    return [
                        $cmd->name => null,
                    ];
                case UndoMoveCommand::class:
                    $board = $this->game->getBoard();
                    if ($board->getHistory()) {
                        $board->undoMove($board->getCastlingAbility());
                        $this->game->setBoard($board);
                    }
                    return [
                        $cmd->name => $this->game->state(),
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
