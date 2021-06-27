<?php

namespace ChessServer\Mode;

use ChessServer\Command\PlayFen;

class PlayFriend extends AbstractMode
{
    const NAME = 'playfriend';

    protected $jwt;

    public function __construct($game, $jwt)
    {
        parent::__construct($game);

        $this->jwt = $jwt;
    }

    public function getJwt()
    {
        return $this->jwt;
    }

    public function res($argv, $cmd)
    {
        parent::res($argv, $cmd);

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
