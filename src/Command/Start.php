<?php

namespace PgnChessServer\Command;

use PGNChess\PGN\Symbol;
use PgnChessServer\AbstractCommand;
use PgnChessServer\Mode\AiMode;
use PgnChessServer\Mode\DatabaseMode;
use PgnChessServer\Mode\PlayerMode;
use PgnChessServer\Mode\TrainingMode;

class Start extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/start';
        $this->description = 'Starts a new game. The "color" parameter is not required in training mode.';
        $this->params = [
            'mode' => [
                AiMode::NAME,
                DatabaseMode::NAME,
                PlayerMode::NAME,
                TrainingMode::NAME,
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
                case TrainingMode::NAME:
                    return count($argv) - 1 === count($this->params) - 1;
                default:
                    return count($argv) - 1 === count($this->params) && in_array($argv[2], $this->params['color']);
            }
        }

        return false;
    }
}
