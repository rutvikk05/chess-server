<?php

namespace ChessServer\GameMode;

use Chess\Game;
use ChessServer\Command\HeuristicPictureCommand;

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
                case HeuristicPictureCommand::class:
                    return [
                        $cmd->name => [
                            'dimensions' => (new \Chess\HeuristicPicture(''))->getDimensions(),
                            'balance' => $this->game->heuristicPicture(true, $this->fen),
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
