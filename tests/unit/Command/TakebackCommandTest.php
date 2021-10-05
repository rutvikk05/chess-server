<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\TakebackCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class TakebackCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_takeback_accept()
    {
        $this->assertInstanceOf(
            TakebackCommand::class,
            self::$parser->validate('/takeback accept')
        );
    }

    /**
     * @test
     */
    public function validate_takeback_decline()
    {
        $this->assertInstanceOf(
            TakebackCommand::class,
            self::$parser->validate('/takeback decline')
        );
    }

    /**
     * @test
     */
    public function validate_takeback_proposes()
    {
        $this->assertInstanceOf(
            TakebackCommand::class,
            self::$parser->validate('/takeback propose')
        );
    }

    /**
     * @test
     */
    public function validate_takeback_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/takeback foo');
    }

    /**
     * @test
     */
    public function validate_takeback_bar()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/takeback bar');
    }

    /**
     * @test
     */
    public function validate_takeback_accept_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/takeback accept foo');
    }
}
