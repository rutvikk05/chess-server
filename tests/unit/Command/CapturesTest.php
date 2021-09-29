<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\CapturesCommand;
use ChessServer\Tests\Unit\CommandTestCase;

class CapturesTest extends CommandTestCase
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
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_captures_foo()
    {
        self::$parser->validate('/captures foo');
    }
}
