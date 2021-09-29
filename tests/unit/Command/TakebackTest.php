<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Takeback;
use ChessServer\Tests\Unit\CommandTestCase;

class TakebackTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_takeback_accept()
    {
        $this->assertInstanceOf(
            Takeback::class,
            self::$parser->validate('/takeback accept')
        );
    }

    /**
     * @test
     */
    public function validate_takeback_decline()
    {
        $this->assertInstanceOf(
            Takeback::class,
            self::$parser->validate('/takeback decline')
        );
    }

    /**
     * @test
     */
    public function validate_takeback_proposes()
    {
        $this->assertInstanceOf(
            Takeback::class,
            self::$parser->validate('/takeback propose')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_takeback_foo()
    {
        self::$parser->validate('/takeback foo');
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_takeback_bar()
    {
        self::$parser->validate('/takeback bar');
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_takeback_accept_foo()
    {
        self::$parser->validate('/takeback accept foo');
    }
}
