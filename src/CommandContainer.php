<?php

namespace ChessServer;

use ChessServer\Command\AcceptFriendRequestCommand;
use ChessServer\Command\AsciiCommand;
use ChessServer\Command\CastlingCommand;
use ChessServer\Command\CapturesCommand;
use ChessServer\Command\FenCommand;
use ChessServer\Command\HeuristicPictureCommand;
use ChessServer\Command\HistoryCommand;
use ChessServer\Command\IsCheckCommand;
use ChessServer\Command\IsMateCommand;
use ChessServer\Command\PieceCommand;
use ChessServer\Command\PiecesCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\QuitCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\StatusCommand;

class CommandContainer
{
    private $obj;

    public function __construct()
    {
        $this->obj = new \SplObjectStorage;
        $this->obj->attach(new AcceptFriendRequest());
        $this->obj->attach(new Ascii());
        $this->obj->attach(new Castling());
        $this->obj->attach(new Captures());
        $this->obj->attach(new Fen());
        $this->obj->attach(new HeuristicPicture());
        $this->obj->attach(new History());
        $this->obj->attach(new IsCheck());
        $this->obj->attach(new IsMate());
        $this->obj->attach(new Piece());
        $this->obj->attach(new Pieces());
        $this->obj->attach(new PlayFen());
        $this->obj->attach(new Quit());
        $this->obj->attach(new Start());
        $this->obj->attach(new Status());
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
