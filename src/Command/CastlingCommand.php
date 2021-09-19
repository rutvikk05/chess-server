<?php

namespace ChessServer\Command;

class CastlingCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/castling';
        $this->description = 'Gets the castling status.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
