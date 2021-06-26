<?php

namespace ChessServer\Mode;

use ChessServer\Command\PlayFen;

class Friend extends AbstractMode
{
    const NAME = 'friend';

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
