<?php

namespace ChessServer\Command;

class HeuristicPictureCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristic_picture';
        $this->description = "Takes a balanced heuristic picture of the current game.";
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
