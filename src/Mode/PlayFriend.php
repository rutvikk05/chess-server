<?php

namespace ChessServer\Mode;

use ChessServer\Command\PlayFen;

class PlayFriend extends AbstractMode
{
    const NAME = 'playfriend';

    protected $jwt;

    protected $hash;

    public function __construct($game, $jwt)
    {
        parent::__construct($game);

        $this->jwt = $jwt;
        $this->hash = md5($jwt);
    }

    public function getJwt()
    {
        return $this->jwt;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function res($argv, $cmd)
    {
        if ($res = parent::res($argv, $cmd)) {
            return $res;
        }

        try {
            switch (get_class($cmd)) {
                case PlayFen::class:
                    // TODO:
                    // at this moment this is a copy of the /analysis mode
                    return [
                        'playfen' => [
                          'legal' => $this->game->playFen($argv[1]),
                          'movetext' => $this->game->movetext(),
                        ],
                    ];
            }
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
            ];
        }
    }
}
