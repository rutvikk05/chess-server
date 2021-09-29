<?php

namespace ChessServer\GameMode;

use Chess\Game;
use ChessServer\Command\TakebackCommand;

class PlayFriendMode extends AbstractMode
{
    const NAME = 'playfriend';

    protected $jwt;

    public function __construct(Game $game, array $resourceIds, string $jwt)
    {
        parent::__construct($game, $resourceIds);

        $this->jwt = $jwt;
        $this->hash = md5($jwt);
    }

    public function getJwt()
    {
        return $this->jwt;
    }

    public function res($argv, $cmd)
    {
        try {
            switch (get_class($cmd)) {
                case TakebackCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                default:
                    return parent::res($argv, $cmd);
            }
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
