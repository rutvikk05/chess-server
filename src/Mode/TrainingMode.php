<?php

namespace PgnChessServer\Mode;

use PgnChessServer\AbstractMode;
use PgnChessServer\Command\Play;

class TrainingMode extends AbstractMode
{
    const NAME = 'training';

    public function res()
    {
        try {
            if (is_a($this->cmd, Play::class)) {
                return [
                    'legal' => $this->game->play($this->argv[1], $this->argv[2]),
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
