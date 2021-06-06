<?php

namespace ChessServer\Command;

use Chess\PGN\Symbol;
use ChessServer\AbstractCommand;

class PlayFen extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/playfen';
        $this->description = 'Plays a chess move on the board. All parameters are mandatory.';
        $this->params = [
            'from' => 'FEN',
        ];
        $this->dependsOn = [
            Start::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
