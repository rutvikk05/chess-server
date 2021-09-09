<?php

namespace ChessServer\Command;

class HistoryCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/history';
        $this->description = "The current game's history.";
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
