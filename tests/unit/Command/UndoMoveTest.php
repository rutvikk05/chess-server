<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\UndoMoveCommand;
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
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_undomove_foo()
    {
        self::$parser->validate('/undomove foo');
    }
}
