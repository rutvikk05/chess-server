<?php

namespace PgnChessServer;

use PgnChessServer\Command\Start;

class CommandParser
{
    public static $argv;

    public static function filter($string)
    {
        return array_map('trim', explode(' ', $string));
    }

    public static function validate($string)
    {
        self::$argv = self::filter($string);

        switch (self::$argv[0]) {
            case Start::$name:
                return count(self::$argv) -1 === count(Start::$params) &&
                    in_array(self::$argv[1], Start::$params['mode']);
            default:
                return false;
        }
    }

    public static function argv($string)
    {
        return self::filter($string);
    }
}
