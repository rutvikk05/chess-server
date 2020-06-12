<?php

namespace PgnChessServer\Mode;

use PgnChessServer\AbstractMode;
use PgnChessServer\Command\Play;

class TrainingMode extends AbstractMode
{
    const NAME = 'training';

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
