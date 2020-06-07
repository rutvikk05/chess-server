<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Start;
use PgnChessServer\Tests\Unit\CommandTestCase;

class StartTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_start_ai_w()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start ai w')
        );
    }

    /**
     * @test
     */
    public function validate_start_ai_b()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start ai b')
        );
    }

    /**
     * @test
     */
    public function validate_start_database_w()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start database w')
        );
    }

    /**
     * @test
     */
    public function validate_start_database_b()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start database b')
        );
    }

    /**
     * @test
     */
    public function validate_start_player_w()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start player w')
        );
    }

    /**
     * @test
     */
    public function validate_start_player_b()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start player b')
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
    public function validate_start_ai()
    {
        self::$parser->validate('/start ai');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_ai_w_b()
    {
        self::$parser->validate('/start ai w b');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_database()
    {
        self::$parser->validate('/start database');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_database_w_b()
    {
        self::$parser->validate('/start database w b');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_player()
    {
        self::$parser->validate('/start player');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_player_w_b()
    {
        self::$parser->validate('/start player w b');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_training_w()
    {
        self::$parser->validate('/start training w');
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
}
