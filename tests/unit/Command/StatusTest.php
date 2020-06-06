<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Status;
use PgnChessServer\Tests\Unit\CommandTestCase;

class StatusTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_status()
    {
        $this->assertInstanceOf(
            Status::class,
            self::$parser->validate('/status')
        );
    }

    /**
     * @test
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_status_foo()
    {
        self::$parser->validate('/status foo');
    }
}
