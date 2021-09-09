<?php

namespace ChessServer\Command;

class PieceCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/piece';
        $this->description = 'Gets a piece by its position on the board.';
        $this->params = [
            'position' => 'string',
        ];
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
