<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Quit;
use ChessServer\Tests\Unit\CommandTestCase;

class QuitTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_quit()
    {
        $this->assertInstanceOf(
            Quit::class,
            self::$parser->validate('/quit')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_quit_foo()
    {
        self::$parser->validate('/quit foo');
    }
}
