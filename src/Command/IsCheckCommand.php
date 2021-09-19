<?php

namespace ChessServer\Command;

class IsCheckCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/ischeck';
        $this->description = 'Finds out if the game is in check.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
