<?php

namespace ChessServer\Command;

class RematchCommand extends AbstractCommand
{
    const ACTION_ACCEPT    = 'accept';

    const ACTION_DECLINE   = 'decline';

    const ACTION_PROPOSE   = 'propose';

    public function __construct()
    {
        $this->name = '/rematch';
        $this->description = 'Allows to offer a rematch.';
        $this->params = [
            // mandatory param
            'action' => [
                self::ACTION_ACCEPT,
                self::ACTION_DECLINE,
                self::ACTION_PROPOSE,
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
