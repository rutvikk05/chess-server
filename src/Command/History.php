<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class History
{
    use CommandTrait;

    public static $name = '/history';

    public static $description = "The current game's history.";
}
