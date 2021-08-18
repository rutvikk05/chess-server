<?php

namespace ChessServer\Command;

class HeuristicPicture extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristicpicture';
        $this->description = "Takes a balanced heuristic picture of the current game.";
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
