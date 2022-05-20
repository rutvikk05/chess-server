<?php

namespace ChessServer\Command;

use Chess\PGN\AN\Color;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\GrandmasterMode;
use ChessServer\GameMode\LoadFenMode;
use ChessServer\GameMode\LoadPgnMode;
use ChessServer\GameMode\PlayMode;

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
                GrandmasterMode::NAME,
                LoadFenMode::NAME,
                LoadPgnMode::NAME,
                PlayMode::NAME,
            ],
            // LoadFenMode
            // optional param
            'fen' => 'string',
            // LoadPgnMode
            // optional param
            'movetext' => 'string',
            // GrandmasterMode, PlayMode
            // optional param
            'color' => [
                Color::W,
                Color::B,
            ],
            // optional param
            'min' => 'int',
            // optional param
            'increment' => 'int',
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['mode'])) {
            switch ($argv[1]) {
                case AnalysisMode::NAME:
                    return count($argv) - 1 === 1;
                case GrandmasterMode::NAME:
                    return count($argv) - 1 === 2;
                case LoadFenMode::NAME:
                    return count($argv) - 1 === 2;
                case LoadPgnMode::NAME:
                    return count($argv) - 1 === 2;
                case PlayMode::NAME:
                    return count($argv) - 1 === 4;
                default:
                    // do nothing
                    break;
            }
        }

        return false;
    }
}
