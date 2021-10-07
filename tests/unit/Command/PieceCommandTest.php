<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\PieceCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class PieceCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_piece_e4()
    {
        $this->assertInstanceOf(
            PieceCommand::class,
            self::$parser->validate('/piece e4')
        );
    }

    /**
     * @test
     */
    public function validate_piece_h1()
    {
        $this->assertInstanceOf(
            PieceCommand::class,
            self::$parser->validate('/piece h1')
        );
    }

    /**
     * @test
     */
    public function validate_piece_e4_e5()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/piece e4 e5');
    }
}
