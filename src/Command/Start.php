<?php

namespace ChessServer\Command;

use Chess\PGN\Symbol;
use ChessServer\Mode\Analysis;
use ChessServer\Mode\Fen;
use ChessServer\Mode\PlayFriend;

class Start extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/start';
        $this->description = 'Starts a new game.';
        $this->params = [
            'mode' => [
                Analysis::NAME,
                Fen::NAME,
                PlayFriend::NAME,
            ],
            'fen' => 'string',  // FEN mode
            'jwt' => 'string',  // PlayFriend mode
            'hash' => 'string', // PlayFriend  mode
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['mode'])) {
            switch ($argv[1]) {
                case Analysis::NAME:
                    return count($argv) - 1 === 1;
                case Fen::NAME:
                    return count($argv) - 1 === 2;
                case PlayFriend::NAME:
                    return count($argv) - 1 === 3; 
                default:
                    // do nothing
                    break;
            }
        }

        return false;
    }
}
