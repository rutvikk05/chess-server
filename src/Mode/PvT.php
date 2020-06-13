<?php

namespace PgnChessServer\Mode;

use PgnChessServer\AbstractMode;
use PgnChessServer\Command\Play;

class PvT extends AbstractMode
{
    /** player vs themselves */
    const NAME = 'pvt';

    public function res($argv, $cmd)
    {
        try {
            if (is_a($cmd, Play::class)) {
                return [
                    'legal' => $this->game->play($argv[1], $argv[2]),
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
