<?php

namespace ChessServer\Command;

class AcceptPlayRequestCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/accept';
        $this->description = 'Accepts a request to play a game.';
        $this->params = [
            'jwt' => '<string>',
        ];
        $this->dependsOn = [];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
