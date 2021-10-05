<?php

namespace ChessServer\Command;

class EventsCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/events';
        $this->description = 'Gets the events taking place on the game.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
