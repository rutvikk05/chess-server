<?php

namespace ChessServer\Command;

class IsMateCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/ismate';
        $this->description = 'Finds out if the game is over.';
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
