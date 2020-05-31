<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\CommandParser;
use PgnChessServer\Command\Help;
use PHPUnit\Framework\TestCase;

class HelpTest extends TestCase
{
    /**
     * @test
     */
    public function validate_help()
    {
        $this->assertTrue(
            CommandParser::validate('/help')
        );
    }

    /**
     * @test
     */
    public function validate_help_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/help foo')
        );
    }
}
