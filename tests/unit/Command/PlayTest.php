<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\CommandParser;
use PHPUnit\Framework\TestCase;

class CommandParserTest extends TestCase
{
    /**
     * @test
     */
    public function validate_play_w_e4()
    {
        $this->assertTrue(
            CommandParser::validate('/play w e4')
        );
    }

    /**
     * @test
     */
    public function validate_play_b_e5()
    {
        $this->assertTrue(
            CommandParser::validate('/play w e4')
        );
    }

    /**
     * @test
     */
    public function validate_play_w_d3_d5()
    {
        $this->assertFalse(
            CommandParser::validate('/play w d3 d5')
        );
    }

    /**
     * @test
     */
    public function validate_play_foo_bar()
    {
        $this->assertFalse(
            CommandParser::validate('/play foo bar')
        );
    }

    /**
     * @test
     */
    public function validate_play()
    {
        $this->assertFalse(
            CommandParser::validate('/play')
        );
    }
}
