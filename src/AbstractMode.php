<?php

namespace ChessServer;

use Chess\PGN\Symbol;
use ChessServer\Command\Ascii;
use ChessServer\Command\Captures;
use ChessServer\Command\Fen;
use ChessServer\Command\History;
use ChessServer\Command\IsCheck;
use ChessServer\Command\IsMate;
use ChessServer\Command\Piece;
use ChessServer\Command\Pieces;
use ChessServer\Command\Play;
use ChessServer\Command\PlayFen;
use ChessServer\Command\Status;

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
                case Ascii::class:
                    return [
                        'ascii' => $this->game->ascii(),
                    ];
                case Fen::class:
                    return [
                        'fen' => $this->game->fen(),
                    ];
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
                case Piece::class:
                    return [
                        'piece' => $this->game->piece($argv[1]),
                    ];
                case Pieces::class:
                    return [
                        'pieces' => $this->game->pieces($argv[1]),
                    ];
                case Play::class:
                    return [
                        'legal' => $this->game->play($argv[1], $argv[2]),
                    ];
                case PlayFen::class:
                    return [
                        'legal' => $this->game->playFen($argv[1]),
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
}
