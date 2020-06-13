<?php

namespace PgnChessServer\Mode;

use PGNChess\PGN\Symbol;
use PgnChessServer\AbstractMode;
use PgnChessServer\Command\Play;

class PvD extends AbstractMode
{
    /** player vs database */
    const NAME = 'pvd';

    protected $color;

    public function __construct($argv, $cmd, $game)
    {
        parent::__construct($game);

        $this->color = $argv[2];

        // play the first move if the opponent's color is white
        if ($argv[2] === Symbol::BLACK) {
            $movetext = explode(' ', $this->game->metadata()['movetext']);
            $pgn = explode('.', $movetext[0])[1];
            $this->game->play(Symbol::WHITE, $pgn);
        }
    }

    public function res($argv, $cmd)
    {
        try {
            if (is_a($cmd, Play::class)) {
                if ($this->game->play($argv[1], $argv[2])) {
                    if ($this->autoPlay()) {
                        $last = $this->game->history()[count($this->game->history())-1];
                        return [
                            'I' => $argv[1] . ' ' . $argv[2],
                            'database' => $last->color . ' ' . $last->pgn,
                        ];
                    }
                    return [
                        'I' => $argv[1] . ' ' . $argv[2],
                        'database' => null,
                        'message' => "Mmm, sorry. There are no chess moves left in the database.",
                    ];
                } else {
                    return [
                        'message' => "Whoops! It seems as if this is not a legal chess move. Please try again.",
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
            ];
        }

        return parent::res($argv, $cmd);
    }

    protected function autoPlay()
    {
        $movetext = explode(' ', $this->game->metadata()['movetext']);
        $i = array_key_last($this->game->history()) + 1;
        if (isset($movetext[$i])) {
            $this->color === Symbol::WHITE ? $pgn = $movetext[$i] : $pgn = explode('.', $movetext[$i])[1];
            $this->game->play($this->getOppositeColor($this->color), $pgn);
            return $this;
        }

        return null;
    }
}
