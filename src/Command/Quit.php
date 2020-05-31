<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class Quit
{
    use CommandTrait;

    public static $name = '/quit';

    public static $description = 'Quits a game.';
}
