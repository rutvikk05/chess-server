<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Parser\CommandParser;
use PHPUnit\Framework\TestCase;

class PiecesTest extends TestCase
{
    /**
     * @test
     */
    public function validate_pieces_w()
    {
        $this->assertTrue(
            CommandParser::validate('/pieces w')
        );
    }

    /**
     * @test
     */
    public function validate_pieces_b()
    {
        $this->assertTrue(
            CommandParser::validate('/pieces b')
        );
    }

    /**
     * @test
     */
    public function validate_pieces_w_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/pieces w foo')
        );
    }

    /**
     * @test
     */
    public function validate_pieces_b_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/pieces b foo')
        );
    }
}
