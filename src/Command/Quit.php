<?php

namespace ChessServer\Command;

use ChessServer\AbstractCommand;

class Quit extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/quit';
        $this->description = 'Quits a game.';
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
