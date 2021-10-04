<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\UndoMoveCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class UndoMoveTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_undomove()
    {
        $this->assertInstanceOf(
            UndoMoveCommand::class,
            self::$parser->validate('/undomove')
        );
    }

    /**
     * @test
     */
    public function validate_undomove_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/undomove foo');
    }
}
