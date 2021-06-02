<?php

namespace ChessServer\Mode;

use ChessServer\AbstractMode;
use ChessServer\Command\Play;

class PvA extends AbstractMode
{
    /** player vs ai */
    const NAME = 'pva';

    public function __construct($argv, $cmd, $game)
    {
        parent::__construct($game);

        // TODO
        // play the first move if the opponent's color is white
    }

    public function res($argv, $cmd)
    {
        try {
            if (is_a($this->cmd, Play::class)) {
                // TODO
                return [
                    'message' => 'Soon available! Please be patient.',
                ];
            }
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
            ];
        }

        return parent::res();
    }
}
