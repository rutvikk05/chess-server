<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Pieces;
use ChessServer\Tests\Unit\CommandTestCase;

class PiecesTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_pieces_w()
    {
        $this->assertInstanceOf(
            Pieces::class,
            self::$parser->validate('/pieces w')
        );
    }

    /**
     * @test
     */
    public function validate_pieces_b()
    {
        $this->assertInstanceOf(
            Pieces::class,
            self::$parser->validate('/pieces b')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_pieces_w_foo()
    {
        self::$parser->validate('/pieces w foo');
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_pieces_b_foo()
    {
        self::$parser->validate('/pieces b foo');
    }
}
