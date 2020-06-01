<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class Pieces
{
    use CommandTrait;

    public static $name = '/pieces';

    public static $description = 'Gets the pieces on the board by color.';

    public static $params = [
        'color' => [
            'w',
            'b',
        ],
    ];
}
