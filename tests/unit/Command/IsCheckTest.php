<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\IsCheck;
use PgnChessServer\Tests\Unit\CommandTestCase;

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
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_ischeck_foo()
    {
        self::$parser->validate('/ischeck foo');
    }
}
