<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\PiecesCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class PiecesTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_pieces_w()
    {
        $this->assertInstanceOf(
            PiecesCommand::class,
            self::$parser->validate('/pieces w')
        );
    }

    /**
     * @test
     */
    public function validate_pieces_b()
    {
        $this->assertInstanceOf(
            PiecesCommand::class,
            self::$parser->validate('/pieces b')
        );
    }

    /**
     * @test
     */
    public function validate_pieces_w_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/pieces w foo');
    }

    /**
     * @test
     */
    public function validate_pieces_b_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/pieces b foo');
    }
}
