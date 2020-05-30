<?php

namespace PgnChessServer\Command;

use PgnChessServer\AbstractCommand;

class Metadata extends AbstractCommand
{
    public static $name = '/metadata';

    public static $description = 'Metadata of the current game.';
}
