<?php

namespace PgnChessServer\Command;

use PgnChessServer\AbstractCommand;

class Status extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/status';
        $this->description = 'The current game status.';
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
