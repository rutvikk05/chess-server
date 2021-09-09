<?php

namespace ChessServer\Command;

class CastlingCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/castling';
        $this->description = 'Gets the castling status.';
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
