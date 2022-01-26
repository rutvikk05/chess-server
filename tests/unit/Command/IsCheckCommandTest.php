<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\IsCheckCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class IsCheckCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_is_check()
    {
        $this->assertInstanceOf(
            IsCheckCommand::class,
            self::$parser->validate('/is_check')
        );
    }

    /**
     * @test
     */
    public function validate_is_check_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/is_check foo');
    }
}
