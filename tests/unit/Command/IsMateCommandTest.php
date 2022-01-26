<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\IsMateCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class IsMateCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_is_mate()
    {
        $this->assertInstanceOf(
            IsMateCommand::class,
            self::$parser->validate('/is_mate')
        );
    }

    /**
     * @test
     */
    public function validate_is_mate_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/is_mate foo');
    }
}
