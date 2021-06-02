<?php

namespace ChessServer\Command;

use ChessServer\AbstractCommand;

class Captures extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/captures';
        $this->description = 'Gets the pieces captured by both players.';
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
