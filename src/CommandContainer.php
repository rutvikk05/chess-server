<?php

namespace PgnChessServer;

use PgnChessServer\Command\Captures;
use PgnChessServer\Command\Help;
use PgnChessServer\Command\History;
use PgnChessServer\Command\IsCheck;
use PgnChessServer\Command\IsMate;
use PgnChessServer\Command\Metadata;
use PgnChessServer\Command\Piece;
use PgnChessServer\Command\Pieces;
use PgnChessServer\Command\Play;
use PgnChessServer\Command\Quit;
use PgnChessServer\Command\Start;
use PgnChessServer\Command\Status;

class CommandContainer
{
    private $obj;

    public function __construct()
    {
        $this->obj = new \SplObjectStorage;
        $this->obj->attach(new Captures);
        $this->obj->attach(new Help);
        $this->obj->attach(new History);
        $this->obj->attach(new IsCheck);
        $this->obj->attach(new IsMate);
        $this->obj->attach(new Metadata);
        $this->obj->attach(new Piece);
        $this->obj->attach(new Pieces);
        $this->obj->attach(new Play);
        $this->obj->attach(new Quit);
        $this->obj->attach(new Start);
        $this->obj->attach(new Status);
    }

    public function findByName(string $name)
    {
        $this->obj->rewind();
        while ($this->obj->valid()) {
            if ($this->obj->current()->name === $name) {
                return $this->obj->current();
            }
            $this->obj->next();
        }

        return null;
    }

    public function help()
    {
        $o = '';
        $this->obj->rewind();
        while ($this->obj->valid()) {
            $o .= $this->obj->current()->name;
            $this->obj->current()->params ? $o .= ' ' . json_encode($this->obj->current()->params) : null;
            $o .= ' ' . $this->obj->current()->description . PHP_EOL;
            $this->obj->next();
        }

        return $o;
    }
}
