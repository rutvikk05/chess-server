<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\UndoMoveCommand;
use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class UndoMoveCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_undo_move()
    {
        $this->assertInstanceOf(
            UndoMoveCommand::class,
            self::$parser->validate('/undo_move')
        );
    }

    /**
     * @test
     */
    public function validate_undo_move_foo()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/undo_move foo');
    }
}
