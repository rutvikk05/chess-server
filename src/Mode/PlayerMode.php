<?php

namespace PgnChessServer\Mode;

use PgnChessServer\AbstractMode;
use PgnChessServer\Command\Play;

class PlayerMode extends AbstractMode
{
    const NAME = 'player';

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
