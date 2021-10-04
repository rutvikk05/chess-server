<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\EventCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class EventTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_event()
    {
        $this->assertInstanceOf(
            EventCommand::class,
            self::$parser->validate('/event')
        );
    }

    /**
     * @test
     */
    public function validate_event_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/event foo');
    }
}
