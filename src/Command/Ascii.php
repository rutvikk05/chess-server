<?php

namespace ChessServer\Command;

use ChessServer\AbstractCommand;

class Ascii extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/ascii';
        $this->description = 'Prints the ASCII representation of the game.';
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
