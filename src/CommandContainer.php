<?php

namespace ChessServer;

use ChessServer\Command\AcceptFriendRequestCommand;
use ChessServer\Command\AsciiCommand;
use ChessServer\Command\CastlingCommand;
use ChessServer\Command\CapturesCommand;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\FenCommand;
use ChessServer\Command\HeuristicPictureCommand;
use ChessServer\Command\HistoryCommand;
use ChessServer\Command\IsCheckCommand;
use ChessServer\Command\IsMateCommand;
use ChessServer\Command\PieceCommand;
use ChessServer\Command\PiecesCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\QuitCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\StatusCommand;
use ChessServer\Command\TakebackCommand;
use ChessServer\Command\UndoMoveCommand;

class CommandContainer
{
    private $obj;

    public function __construct()
    {
        $this->obj = new \SplObjectStorage;
        $this->obj->attach(new AcceptFriendRequestCommand());
        $this->obj->attach(new AsciiCommand());
        $this->obj->attach(new CastlingCommand());
        $this->obj->attach(new CapturesCommand());
        $this->obj->attach(new DrawCommand());
        $this->obj->attach(new FenCommand());
        $this->obj->attach(new HeuristicPictureCommand());
        $this->obj->attach(new HistoryCommand());
        $this->obj->attach(new IsCheckCommand());
        $this->obj->attach(new IsMateCommand());
        $this->obj->attach(new PieceCommand());
        $this->obj->attach(new PiecesCommand());
        $this->obj->attach(new PlayFenCommand());
        $this->obj->attach(new QuitCommand());
        $this->obj->attach(new ResignCommand());
        $this->obj->attach(new StartCommand());
        $this->obj->attach(new StatusCommand());
        $this->obj->attach(new TakebackCommand());
        $this->obj->attach(new UndoMoveCommand());
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
