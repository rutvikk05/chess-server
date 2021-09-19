<?php

namespace ChessServer\Command;

class AsciiCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/ascii';
        $this->description = 'Prints the ASCII representation of the game.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
