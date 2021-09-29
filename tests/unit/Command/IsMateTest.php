<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\IsMateCommand;
use ChessServer\Tests\Unit\CommandTestCase;

class IsMateTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_ismate()
    {
        $this->assertInstanceOf(
            IsMateCommand::class,
            self::$parser->validate('/ismate')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_ismate_foo()
    {
        self::$parser->validate('/ismate foo');
    }
}
