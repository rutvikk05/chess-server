<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\AsciiCommand;
use ChessServer\Tests\Unit\CommandTestCase;

class AsciiTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_ascii()
    {
        $this->assertInstanceOf(
            AsciiCommand::class,
            self::$parser->validate('/ascii')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_ascii_foo()
    {
        self::$parser->validate('/ascii foo');
    }
}
