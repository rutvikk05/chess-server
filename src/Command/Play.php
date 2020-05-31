<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class Play
{
    use CommandTrait;

    public static $name = '/play';

    public static $description = 'Plays a chess move on the board.';

    public static $params = [
        'color' => [
            'w',
            'b',
        ],
        'pgn' => 'move',
    ];
}
