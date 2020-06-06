<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Start;
use PgnChessServer\Tests\Unit\CommandTestCase;

class StartTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_start_database()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start database')
        );
    }

    /**
     * @test
     */
    public function validate_start_player()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start player')
        );
    }

    /**
     * @test
     */
    public function validate_start_training()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start training')
        );
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_foo()
    {
        self::$parser->validate('/start foo');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_bar()
    {
        self::$parser->validate('/start bar');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_player_training()
    {
        self::$parser->validate('/start player training');
    }
}
