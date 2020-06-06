<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\History;
use PgnChessServer\Tests\Unit\CommandTestCase;

class HistoryTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_history()
    {
        $this->assertInstanceOf(
            History::class,
            self::$parser->validate('/history')
        );
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_history_foo()
    {
        self::$parser->validate('/history foo');
    }
}
