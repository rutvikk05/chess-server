<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class Piece
{
    use CommandTrait;

    public static $name = '/piece';

    public static $description = 'Gets a piece by its position on the board.';

    public static $params = [
        'position' => 'square',
    ];
}
