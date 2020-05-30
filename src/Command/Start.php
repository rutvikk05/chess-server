<?php

namespace PgnChessServer\Command;

class Start
{
    public static $name = '/start';

    public static $description = 'Starts a new game.';

    public static $params = [
        'mode' => [
            'train',
            'foe',
            'database',
        ],
    ];
}
