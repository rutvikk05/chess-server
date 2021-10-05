<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\EventsCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class EventsCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_events()
    {
        $this->assertInstanceOf(
            EventsCommand::class,
            self::$parser->validate('/events')
        );
    }

    /**
     * @test
     */
    public function validate_events_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/events foo');
    }
}
