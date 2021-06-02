<?php

namespace ChessServer\Command;

use PGNChess\PGN\Symbol;
use ChessServer\AbstractCommand;

class Play extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/play';
        $this->description = 'Plays a chess move on the board. All parameters are mandatory.';
        $this->params = [
            'color' => [
                Symbol::WHITE,
                Symbol::BLACK,
            ],
            'pgn' => 'move',
        ];
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params) && in_array($argv[1], $this->params['color']);
    }
}
