<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class Start
{
    use CommandTrait;

    public static $name = '/start';

    public static $description = 'Starts a new game.';

    public static $params = [
        'mode' => [
            'database',
            'player',
            'training',
        ],
    ];
}
