<?php

namespace ChessServer\Command;

class GrandmasterCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/gm';
        $this->description = 'Returns a computer generated response to the current position.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
