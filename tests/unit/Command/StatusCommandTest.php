<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\StatusCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class StatusCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_status()
    {
        $this->assertInstanceOf(
            StatusCommand::class,
            self::$parser->validate('/status')
        );
    }

    /**
     * @test
     */
    public function validate_status_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/status foo');
    }
}
