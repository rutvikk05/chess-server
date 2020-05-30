<?php

namespace PgnChessServer\Command;

use PgnChessServer\AbstractCommand;

class Quit extends AbstractCommand
{
    public static $name = '/quit';

    public static $description = 'Quits a game.';
}
