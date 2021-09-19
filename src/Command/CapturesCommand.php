<?php

namespace ChessServer\Command;

class CapturesCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/captures';
        $this->description = 'Gets the pieces captured by both players.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
