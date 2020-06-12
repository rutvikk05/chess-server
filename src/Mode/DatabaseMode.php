<?php

namespace PgnChessServer\Mode;

use PGNChess\PGN\Symbol;
use PgnChessServer\AbstractMode;
use PgnChessServer\Command\Play;

class DatabaseMode extends AbstractMode
{
    const NAME = 'database';

    protected $move;

    public function __construct($argv, $cmd, $game)
    {
        parent::__construct($game);

        $movetext = explode(' ', $this->game->metadata()['movetext']);
        
        if ($argv[2] === Symbol::BLACK) {
            $pgn = explode('.', $movetext[0])[1];
            $this->game->play(Symbol::WHITE, $pgn);
            $this->move = Symbol::WHITE . ' ' . $pgn;
        }
    }

    public function getMove()
    {
        return $this->move;
    }

    public function res($argv, $cmd)
    {
        try {
            if (is_a($cmd, Play::class)) {
                // TODO
                return [
                    'message' => 'Soon available! Please be patient.',
                ];
            }
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
            ];
        }

        return parent::res();
    }
}
