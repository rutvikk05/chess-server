<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Piece;
use ChessServer\Tests\Unit\CommandTestCase;

class PieceTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_piece_e4()
    {
        $this->assertInstanceOf(
            Piece::class,
            self::$parser->validate('/piece e4')
        );
    }

    /**
     * @test
     */
    public function validate_piece_h1()
    {
        $this->assertInstanceOf(
            Piece::class,
            self::$parser->validate('/piece h1')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_piece_e4_e5()
    {
        self::$parser->validate('/piece e4 e5');
    }
}
