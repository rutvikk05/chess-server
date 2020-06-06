<?php

namespace PgnChessServer\Command;

use PgnChessServer\AbstractCommand;

class Start extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/start';
        $this->description = 'Starts a new game.';
        $this->params = [
            'mode' => [
                'database',
                'player',
                'training',
            ],
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params) && in_array($argv[1], $this->params['mode']);
    }
}
