<?php

namespace PgnChessServer\Tests\Unit;

use PgnChessServer\CommandParser;
use PgnChessServer\Command\Start;
use PHPUnit\Framework\TestCase;

class CommandParserTest extends TestCase
{
    /**
     * @test
     */
    public function start()
    {
        $this->assertInstanceOf(
            Start::class,
            CommandParser::parse('/start foo')
        );
    }
}
