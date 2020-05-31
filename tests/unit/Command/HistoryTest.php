<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Parser\CommandParser;
use PHPUnit\Framework\TestCase;

class HistoryTest extends TestCase
{
    /**
     * @test
     */
    public function validate_history()
    {
        $this->assertTrue(
            CommandParser::validate('/history')
        );
    }

    /**
     * @test
     */
    public function validate_history_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/history foo')
        );
    }
}
