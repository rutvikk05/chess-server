<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\AsciiCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class AsciiCommandTest extends CommandTestCase
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
     */
    public function validate_ascii_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/ascii foo');
    }
}
