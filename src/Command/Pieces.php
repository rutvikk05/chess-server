<?php

namespace PgnChessServer\Command;

use PGNChess\PGN\Symbol;
use PgnChessServer\AbstractCommand;

class Pieces extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/pieces';
        $this->description = 'Gets the pieces on the board by color.';
        $this->params = [
            'color' => [
                Symbol::WHITE,
                Symbol::BLACK,
            ],
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
