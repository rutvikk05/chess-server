<?php

namespace ChessServer\Command;

use Chess\PGN\Symbol;

class PlayFenCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/playfen';
        $this->description = 'Plays a chess move in shortened FEN format.';
        $this->params = [
            'fen' => 'string',
        ];
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
