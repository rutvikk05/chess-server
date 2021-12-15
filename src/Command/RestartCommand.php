<?php

namespace ChessServer\Command;

class RestartCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/restart';
        $this->description = 'Restarts a game.';
        $this->params = [
            'hash' => 'string',
        ];
        $this->dependsOn = [];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
