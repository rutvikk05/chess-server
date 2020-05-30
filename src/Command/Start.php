<?php

namespace PgnChessServer\Command;

use PgnChessServer\AbstractCommand;

class Start extends AbstractCommand
{
    public static $name = '/start';

    public static $description = 'Starts a new game.';

    public static $params = [
        'mode' => [
            'database',
            'player',
            'train',
        ],
    ];
}
