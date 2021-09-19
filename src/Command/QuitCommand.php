<?php

namespace ChessServer\Command;

class QuitCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/quit';
        $this->description = 'Quits a game.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
