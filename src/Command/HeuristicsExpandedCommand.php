<?php

namespace ChessServer\Command;

class HeuristicsExpandedCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristics_expanded';
        $this->description = "Takes an expanded heuristic picture of the current game.";
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
