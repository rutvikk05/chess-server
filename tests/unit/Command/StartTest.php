<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Start;
use PgnChessServer\Tests\Unit\CommandTestCase;

class StartTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_start_pva_w()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start pva w')
        );
    }

    /**
     * @test
     */
    public function validate_start_pva_b()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start pva b')
        );
    }

    /**
     * @test
     */
    public function validate_start_pvd_w()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start pvd w')
        );
    }

    /**
     * @test
     */
    public function validate_start_pvd_b()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start pvd b')
        );
    }

    /**
     * @test
     */
    public function validate_start_pvp_w()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start pvp w')
        );
    }

    /**
     * @test
     */
    public function validate_start_pvp_b()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start pvp b')
        );
    }

    /**
     * @test
     */
    public function validate_start_pvt()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start pvt')
        );
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_pva()
    {
        self::$parser->validate('/start pva');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_pva_w_b()
    {
        self::$parser->validate('/start pva w b');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_pvd()
    {
        self::$parser->validate('/start pvd');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_pvd_w_b()
    {
        self::$parser->validate('/start pvd w b');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_pvp()
    {
        self::$parser->validate('/start pvp');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_pvp_w_b()
    {
        self::$parser->validate('/start pvp w b');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_start_pvt_w()
    {
        self::$parser->validate('/start pvt w');
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
