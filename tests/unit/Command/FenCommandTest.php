<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\FenCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class FenCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_fen()
    {
        $this->assertInstanceOf(
            FenCommand::class,
            self::$parser->validate('/fen')
        );
    }

    /**
     * @test
     */
    public function validate_fen_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/fen foo');
    }
}
