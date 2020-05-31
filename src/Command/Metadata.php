<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class Metadata
{
    use CommandTrait;

    public static $name = '/metadata';

    public static $description = 'Metadata of the current game.';
}
