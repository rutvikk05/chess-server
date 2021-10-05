<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\IsCheckCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class IsCheckCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_ischeck()
    {
        $this->assertInstanceOf(
            IsCheckCommand::class,
            self::$parser->validate('/ischeck')
        );
    }

    /**
     * @test
     */
    public function validate_ischeck_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/ischeck foo');
    }
}
