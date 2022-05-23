<?php

namespace ChessServer\Command;

class RandomGameCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/random_game';
        $this->description = 'Starts a random game.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
