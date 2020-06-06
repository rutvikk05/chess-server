<?php

namespace PgnChessServer\Tests\Unit;

use PgnChessServer\Parser\CommandParser;
use PHPUnit\Framework\TestCase;

class CommandTestCase extends TestCase
{
    protected static $parser;

    public function setUp()
    {
        self::$parser = new CommandParser();
    }
}
