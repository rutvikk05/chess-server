<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\IsMate;
use PgnChessServer\Tests\Unit\CommandTestCase;

class IsMateTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_ismate()
    {
        $this->assertInstanceOf(
            IsMate::class,
            self::$parser->validate('/ismate')
        );
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_ismate_foo()
    {
        self::$parser->validate('/ismate foo');
    }
}
