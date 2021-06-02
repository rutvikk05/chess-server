<?php

namespace ChessServer\Command;

use ChessServer\AbstractCommand;

class Metadata extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/metadata';
        $this->description = 'Metadata of the current game.';
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
