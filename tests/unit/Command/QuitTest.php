<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\CommandParser;
use PHPUnit\Framework\TestCase;

class QuitTest extends TestCase
{
    /**
     * @test
     */
    public function validate_quit()
    {
        $this->assertTrue(
            CommandParser::validate('/quit')
        );
    }

    /**
     * @test
     */
    public function validate_quit_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/quit foo')
        );
    }
}
