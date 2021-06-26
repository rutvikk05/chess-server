<?php

namespace ChessServer\Mode;

use ChessServer\Command\PlayFen;

class Analysis extends AbstractMode
{
    const NAME = 'analysis';

    public function res($argv, $cmd)
    {
        parent::res($argv, $cmd);

        try {
            switch (get_class($cmd)) {
                case PlayFen::class:
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
