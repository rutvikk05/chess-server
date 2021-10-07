<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\QuitCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class QuitCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_quit()
    {
        $this->assertInstanceOf(
            QuitCommand::class,
            self::$parser->validate('/quit')
        );
    }

    /**
     * @test
     */
    public function validate_quit_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/quit foo');
    }
}
