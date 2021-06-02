<?php

namespace PgnChessServer;

use PGNChess\PGN\Symbol;
use PgnChessServer\Command\Captures;
use PgnChessServer\Command\History;
use PgnChessServer\Command\IsCheck;
use PgnChessServer\Command\IsMate;
use PgnChessServer\Command\Metadata;
use PgnChessServer\Command\Piece;
use PgnChessServer\Command\Pieces;
use PgnChessServer\Command\Status;

abstract class AbstractMode
{
    protected $game;

    public function __construct($game)
    {
        $this->game = $game;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function res($argv, $cmd)
    {
        try {
            switch (get_class($cmd)) {
                case Captures::class:
                    return [
                        'captures' => $this->game->captures(),
                    ];
                case History::class:
                    return [
                        'history' => $this->game->history(),
                    ];
                case IsCheck::class:
                    return [
                        'check' => $this->game->isCheck(),
                    ];
                case IsMate::class:
                    return [
                        'mate' => $this->game->isCheck(),
                    ];
                case Metadata::class:
                    return [
                        'metadata' => $this->metadata(),
                    ];
                case Piece::class:
                    return [
                        'piece' => $this->game->piece($argv[1]),
                    ];
                case Pieces::class:
                    return [
                        'pieces' => $this->game->pieces($argv[1]),
                    ];
                case Status::class:
                    return [
                        'status' => $this->game->status(),
                    ];
            }
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function getOppositeColor($color): string
    {
        if ($color == Symbol::WHITE) {
            return Symbol::BLACK;
        } else {
            return Symbol::WHITE;
        }
    }
}
