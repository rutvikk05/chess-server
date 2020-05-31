<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\CommandParser;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    /**
     * @test
     */
    public function validate_status()
    {
        $this->assertTrue(
            CommandParser::validate('/status')
        );
    }

    /**
     * @test
     */
    public function validate_status_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/status foo')
        );
    }
}
