<?php

namespace PgnChessServer;

use PgnChessServer\Command\Metadata;
use PgnChessServer\Command\Play;
use PgnChessServer\Command\Quit;
use PgnChessServer\Command\Start;
use PgnChessServer\Command\Status;

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
            case Metadata::$name:
                return count(self::$argv) -1 === 0;
            case Play::$name:
                return count(self::$argv) -1 === count(Play::$params) &&
                    in_array(self::$argv[1], Play::$params['color']);
            case Quit::$name:
                return count(self::$argv) -1 === 0;
            case Start::$name:
                return count(self::$argv) -1 === count(Start::$params) &&
                    in_array(self::$argv[1], Start::$params['mode']);
            case Status::$name:
                return count(self::$argv) -1 === 0;
            default:
                return false;
        }
    }

    public static function argv($string)
    {
        return self::filter($string);
    }
}
