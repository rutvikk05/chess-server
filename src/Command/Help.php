<?php

namespace PgnChessServer\Command;

use PgnChessServer\Command\History;
use PgnChessServer\Command\Metadata;
use PgnChessServer\Command\Play;
use PgnChessServer\Command\Quit;
use PgnChessServer\Command\Start;
use PgnChessServer\Command\Status;

class Help
{
    public static $name = '/help';

    public static $description = 'Provides information on the commands available.';

    public static function output ()
    {
        $o = 'Commands available: ' . PHP_EOL
            . History::$name . str_repeat("\t", 9) . History::$description . PHP_EOL
            . Metadata::$name . str_repeat("\t", 9) . Metadata::$description . PHP_EOL
            . Play::$name . ' ' .  Play::printParams() . str_repeat("\t", 7) . Play::$description . PHP_EOL
            . Quit::$name . str_repeat("\t", 10) . Quit::$description . PHP_EOL
            . Start::$name . ' ' .  Start::printParams() . str_repeat("\t", 6) . Start::$description . PHP_EOL
            . Status::$name . str_repeat("\t", 10) . Status::$description . PHP_EOL;

        return $o;
    }
}
