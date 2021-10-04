<?php

namespace ChessServer\Tests\Unit;

use ChessServer\Parser\CommandParser;
use PHPUnit\Framework\TestCase;

class CommandTestCase extends TestCase
{
    protected static $parser;

    public function setUp(): void
    {
        self::$parser = new CommandParser();
    }
}
