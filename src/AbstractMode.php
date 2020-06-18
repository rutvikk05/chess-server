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
use PgnChessServer\Db\Pdo;

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

    /**
     * Fetches from the database the metadata of the game being played.
     *
     * @return array|bool
     */
    protected function metadata()
    {
        $n = 1;
        $movetext = '';
        
        foreach ($this->game->history() as $key => $val) {
            $key % 2 === 0
                ? $movetext .= $n++.".{$val->pgn} "
                : $movetext .= "{$val->pgn} ";
        }

        $movetext = trim($movetext);

        $result = Pdo::getInstance()
                    ->query("SELECT * FROM games WHERE movetext LIKE '$movetext%' ORDER BY RAND() LIMIT 1")
                    ->fetch(\PDO::FETCH_ASSOC);

        is_array($result) ? $result = array_filter($result) : $result = null;

        return $result;
    }
}
