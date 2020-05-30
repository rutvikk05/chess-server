<?php

namespace PgnChessServer\Command;

use PgnChessServer\AbstractCommand;

class Status extends AbstractCommand
{
    public static $name = '/status';

    public static $description = 'The current game status.';
}
