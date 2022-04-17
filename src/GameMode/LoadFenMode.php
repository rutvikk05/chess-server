<?php

namespace ChessServer\GameMode;

use Chess\Game;
use ChessServer\Command\HeuristicsCommand;

class LoadFenMode extends AbstractMode
{
    const NAME = 'loadfen';

    protected $fen;

    public function __construct(Game $game, array $resourceIds, string $fen)
    {
        parent::__construct($game, $resourceIds);

        $this->fen = $fen;
    }

    public function getFen()
    {
        return $this->fen;
    }

    public function res($argv, $cmd)
    {
        try {
            switch (get_class($cmd)) {
                case HeuristicsCommand::class:
                    return [
                        $cmd->name => [
                            'dimensions' => (new \Chess\Heuristics())->getDimensions(),
                            'balance' => $this->game->heuristics(true, $this->fen),
                        ],
                    ];
                default:
                    return parent::res($argv, $cmd);
            }
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
