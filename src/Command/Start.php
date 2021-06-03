<?php

namespace ChessServer\Command;

use Chess\PGN\Symbol;
use ChessServer\AbstractCommand;
use ChessServer\Mode\PvA;
use ChessServer\Mode\PvD;
use ChessServer\Mode\PvP;
use ChessServer\Mode\PvT;

class Start extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/start';
        $this->description = 'Starts a new game. The "color" parameter is not required in pvt (player vs themselves) mode.';
        $this->params = [
            'mode' => [
                PvA::NAME,
                PvD::NAME,
                PvP::NAME,
                PvT::NAME,
            ],
            'color' => [
                Symbol::WHITE,
                Symbol::BLACK,
            ],
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['mode'])) {
            switch ($argv[1]) {
                // second parameter "color" is not required in training mode
                case PvT::NAME:
                    return count($argv) - 1 === count($this->params) - 1;
                default:
                    return count($argv) - 1 === count($this->params) && in_array($argv[2], $this->params['color']);
            }
        }

        return false;
    }
}
