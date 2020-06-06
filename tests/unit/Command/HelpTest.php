<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Help;
use PgnChessServer\Tests\Unit\CommandTestCase;

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
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_help_foo()
    {
        self::$parser->validate('/help foo');
    }
}
