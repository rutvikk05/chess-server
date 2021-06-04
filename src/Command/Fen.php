<?php

namespace ChessServer\Command;

use ChessServer\AbstractCommand;

class Fen extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/fen';
        $this->description = "Prints the FEN string representation of the game.";
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
