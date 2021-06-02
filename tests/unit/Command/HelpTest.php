<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Help;
use ChessServer\Tests\Unit\CommandTestCase;

class HelpTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_help()
    {
        $this->assertInstanceOf(
            Help::class,
            self::$parser->validate('/help')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_help_foo()
    {
        self::$parser->validate('/help foo');
    }
}
