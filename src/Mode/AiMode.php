<?php

namespace PgnChessServer\Mode;

use PgnChessServer\AbstractMode;
use PgnChessServer\Command\Play;

class AiMode extends AbstractMode
{
    const NAME = 'ai';

    public function res()
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
