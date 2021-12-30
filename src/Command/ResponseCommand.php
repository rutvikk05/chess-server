<?php

namespace ChessServer\Command;

class ResponseCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/response';
        $this->description = 'Returns a computer response to the current position.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
