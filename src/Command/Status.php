<?php

namespace PgnChessServer\Command;

use PgnChessServer\CommandTrait;

class Status
{
    use CommandTrait;
    
    public static $name = '/status';

    public static $description = 'The current game status.';
}
