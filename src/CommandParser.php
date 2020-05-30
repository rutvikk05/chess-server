<?php

namespace PgnChessServer;

use PgnChessServer\Command\Start;

class CommandParser
{
    public static function parse($string)
    {
        $command = explode(' ', $string);
        switch ($command[0]) {
            case Start::$name:
                return new Start;
        }
    }
}
