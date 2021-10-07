<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\StartCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class StartCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_start_analysis()
    {
        $this->assertInstanceOf(
            StartCommand::class,
            self::$parser->validate('/start analysis')
        );
    }

    /**
     * @test
     */
    public function validate_start_analysis_w()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/start analysis w');
    }

    /**
     * @test
     */
    public function validate_start_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/start foo');
    }

    /**
     * @test
     */
    public function validate_start_bar()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/start bar');
    }
}
