<?php

namespace ChessServer\Command;

class LeaveCommand extends AbstractCommand
{
    const ACTION_ACCEPT    = 'accept';

    public function __construct()
    {
        $this->name = '/leave';
        $this->description = 'Allows to leave a game.';
        $this->params = [
            // mandatory param
            'action' => [
                self::ACTION_ACCEPT,
            ],
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['action'])) {
            return count($argv) - 1 === count($this->params);
        }

        return false;
    }
}
