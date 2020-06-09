<?php

namespace PgnChessServer;

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
    protected $argv;

    protected $cmd;

    protected $game;

    public function __construct($argv, $cmd, $game)
    {
        $this->argv = $argv;
        $this->cmd = $cmd;
        $this->game = $game;
    }

    public function res()
    {
        try {
            switch (get_class($this->cmd)) {
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
                        'metadata' => $this->game->metadata(),
                    ];
                case Piece::class:
                    return [
                        'piece' => $this->game->piece($this->argv[1]),
                    ];
                case Pieces::class:
                    return [
                        'pieces' => $this->game->pieces($this->argv[1]),
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
