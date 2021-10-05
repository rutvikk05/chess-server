<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\CastlingCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class CastlingTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_castling()
    {
        $this->assertInstanceOf(
            CastlingCommand::class,
            self::$parser->validate('/castling')
        );
    }

    /**
     * @test
     */
    public function validate_castling_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/castling foo');
    }
}
