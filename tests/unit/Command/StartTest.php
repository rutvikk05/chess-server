<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Parser\CommandParser;
use PHPUnit\Framework\TestCase;

class StartTest extends TestCase
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
    public function validate_start_training()
    {
        $this->assertTrue(
            CommandParser::validate('/start training')
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
    public function validate_start_player_training()
    {
        $this->assertFalse(
            CommandParser::validate('/start player training')
        );
    }
}
