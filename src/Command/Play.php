<?php

namespace PgnChessServer\Command;

use PgnChessServer\AbstractCommand;

class Play extends AbstractCommand
{
    public static $name = '/play';

    public static $description = 'Plays a chess move on the board.';

    public static $params = [
        'color' => [
            'w',
            'b',
        ],
        'pgn' => null,
    ];
}
