<?php

namespace PgnChessServer\Tests\Unit;

use PgnChessServer\CommandParser;
use PHPUnit\Framework\TestCase;

class CommandParserTest extends TestCase
{
    /**
     * @test
     */
    public function validate_start_database()
    {
        $this->assertTrue(
            CommandParser::validate('/start database')
        );
    }

    /**
     * @test
     */
    public function validate_start_player()
    {
        $this->assertTrue(
            CommandParser::validate('/start player')
        );
    }

    /**
     * @test
     */
    public function validate_start_train()
    {
        $this->assertTrue(
            CommandParser::validate('/start train')
        );
    }

    /**
     * @test
     */
    public function validate_start_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/start foo')
        );
    }

    /**
     * @test
     */
    public function validate_start_bar()
    {
        $this->assertFalse(
            CommandParser::validate('/start bar')
        );
    }

    /**
     * @test
     */
    public function validate_start_player_train()
    {
        $this->assertFalse(
            CommandParser::validate('/start player train')
        );
    }

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
    public function validate_status()
    {
        $this->assertTrue(
            CommandParser::validate('/status')
        );
    }
}
