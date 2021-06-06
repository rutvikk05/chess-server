<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Start;
use ChessServer\Tests\Unit\CommandTestCase;

class StartTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_start_analysis()
    {
        $this->assertInstanceOf(
            Start::class,
            self::$parser->validate('/start analysis')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_start_analysis_w()
    {
        self::$parser->validate('/start analysis w');
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_start_foo()
    {
        self::$parser->validate('/start foo');
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_start_bar()
    {
        self::$parser->validate('/start bar');
    }
}
