<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Pieces;
use PgnChessServer\Tests\Unit\CommandTestCase;

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
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_pieces_w_foo()
    {
        self::$parser->validate('/pieces w foo');
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_pieces_b_foo()
    {
        self::$parser->validate('/pieces b foo');
    }
}
