<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\StatusCommand;
use ChessServer\Tests\Unit\CommandTestCase;

class StatusTest extends CommandTestCase
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
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_status_foo()
    {
        self::$parser->validate('/status foo');
    }
}
