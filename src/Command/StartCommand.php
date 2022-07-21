<?php

namespace ChessServer\Command;

use Chess\PGN\AN\Color;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\GmMode;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\PgnMode;
use ChessServer\GameMode\PlayMode;
use ChessServer\GameMode\StockfishMode;

class StartCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/start';
        $this->description = 'Starts a new game.';
        $this->params = [
            // mandatory param
            'mode' => [
                AnalysisMode::NAME,
                GmMode::NAME,
                FenMode::NAME,
                PgnMode::NAME,
                PlayMode::NAME,
                StockfishMode::NAME,
            ],
            // FenMode
            // optional param
            'fen' => '<string>',
            // PgnMode
            // optional param
            'movetext' => '<string>',
            // GmMode
            // optional param
            'color' => [
                Color::W,
                Color::B,
            ],
            // PlayMode
            // mandatory param
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['mode'])) {
            switch ($argv[1]) {
                case AnalysisMode::NAME:
                    return count($argv) - 1 === 1;
                case GmMode::NAME:
                    return count($argv) - 1 === 2;
                case FenMode::NAME:
                    return count($argv) - 1 === 2;
                case PgnMode::NAME:
                    return count($argv) - 1 === 2;
                case PlayMode::NAME:
                    return count($argv) - 1 === 2;
                case StockfishMode::NAME:
                    return count($argv) - 1 === 2;
                default:
                    // do nothing
                    break;
            }
        }

        return false;
    }
}
