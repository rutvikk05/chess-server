<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\IsCheck;
use ChessServer\Tests\Unit\CommandTestCase;

class IsCheckTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_ischeck()
    {
        $this->assertInstanceOf(
            IsCheck::class,
            self::$parser->validate('/ischeck')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_ischeck_foo()
    {
        self::$parser->validate('/ischeck foo');
    }
}
