<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Parser\CommandParser;
use PHPUnit\Framework\TestCase;

class CapturesTest extends TestCase
{
    /**
     * @test
     */
    public function validate_captures()
    {
        $this->assertTrue(
            CommandParser::validate('/captures')
        );
    }

    /**
     * @test
     */
    public function validate_captures_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/captures foo')
        );
    }
}
