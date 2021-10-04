<?php

namespace ChessServer\Command;

class EventCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/event';
        $this->description = 'Fetches the event happening in the game.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
