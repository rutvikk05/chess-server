<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\HistoryCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class HistoryCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_history()
    {
        $this->assertInstanceOf(
            HistoryCommand::class,
            self::$parser->validate('/history')
        );
    }

    /**
     * @test
     */
    public function validate_history_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/history foo');
    }
}
