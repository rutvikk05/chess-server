<?php

namespace ChessServer\Command;

class AcceptFriendRequestCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/accept';
        $this->description = 'Accepts a friend request to play a game.';
        $this->params = [
            'id' => 'id',
        ];
        $this->dependsOn = [];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
