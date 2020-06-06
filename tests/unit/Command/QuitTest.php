<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Quit;
use PgnChessServer\Tests\Unit\CommandTestCase;

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
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_quit_foo()
    {
        self::$parser->validate('/quit foo');
    }
}
