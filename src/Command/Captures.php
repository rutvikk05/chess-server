<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class Captures
{
    use CommandTrait;

    public static $name = '/captures';

    public static $description = 'Gets the pieces captured by both players.';
}
