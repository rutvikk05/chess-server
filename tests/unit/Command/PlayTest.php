<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Play;
use ChessServer\Tests\Unit\CommandTestCase;

class PlayTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_play_w_e4()
    {
        $this->assertInstanceOf(
            Play::class,
            self::$parser->validate('/play w e4')
        );
    }

    /**
     * @test
     */
    public function validate_play_b_e5()
    {
        $this->assertInstanceOf(
            Play::class,
            self::$parser->validate('/play b e5')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_play_w_d3_d5()
    {
        self::$parser->validate('/play w d3 d5');
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_play_foo_bar()
    {
        self::$parser->validate('/play foo bar');
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_play()
    {
        self::$parser->validate('/play');
    }
}
