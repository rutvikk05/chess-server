<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\CapturesCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class CapturesCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_captures()
    {
        $this->assertInstanceOf(
            CapturesCommand::class,
            self::$parser->validate('/captures')
        );
    }

    /**
     * @test
     */
    public function validate_captures_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/captures foo');
    }
}
