<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Parser\CommandParser;
use PHPUnit\Framework\TestCase;

class PieceTest extends TestCase
{
    /**
     * @test
     */
    public function validate_piece_e4()
    {
        $this->assertTrue(
            CommandParser::validate('/piece e4')
        );
    }

    /**
     * @test
     */
    public function validate_piece_h1()
    {
        $this->assertTrue(
            CommandParser::validate('/piece h1')
        );
    }

    /**
     * @test
     */
    public function validate_piece_e4_e5()
    {
        $this->assertFalse(
            CommandParser::validate('/piece e4 e5')
        );
    }
}
