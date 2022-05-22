<?php

namespace ChessServer\GameMode;

use Chess\Game;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\LeaveCommand;
use ChessServer\Command\RematchCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\TakebackCommand;

class PlayMode extends AbstractMode
{
    const NAME = 'play';

    const STATE_PENDING = 'pending';

    const STATE_ACCEPTED = 'accepted';

    protected $jwt;

    protected $state;

    public function __construct(Game $game, array $resourceIds, string $jwt)
    {
        parent::__construct($game, $resourceIds);

        $this->jwt = $jwt;
        $this->hash = md5($jwt);
        $this->state = self::STATE_PENDING;
    }

    public function getJwt()
    {
        return $this->jwt;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState(string $state)
    {
        $this->state = $state;

        return $this;
    }

    public function res($argv, $cmd)
    {
        try {
            switch (get_class($cmd)) {
                case DrawCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                case LeaveCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                case RematchCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                case ResignCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
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
