<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Captures;
use PgnChessServer\Tests\Unit\CommandTestCase;

class CapturesTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_captures()
    {
        $this->assertInstanceOf(
            Captures::class,
            self::$parser->validate('/captures')
        );
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_captures_foo()
    {
        self::$parser->validate('/captures foo');
    }
}
