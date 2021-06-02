<?php

namespace ChessServer\Mode;

use ChessServer\AbstractMode;
use ChessServer\Command\Play;

class PvP extends AbstractMode
{
    /** player vs player */
    const NAME = 'pvp';

    public function res($argv, $cmd)
    {
        try {
            if (is_a($cmd, Play::class)) {
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
