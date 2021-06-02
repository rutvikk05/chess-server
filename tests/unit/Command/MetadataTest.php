<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Metadata;
use ChessServer\Tests\Unit\CommandTestCase;

class MetadataTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_metadata()
    {
        $this->assertInstanceOf(
            Metadata::class,
            self::$parser->validate('/metadata')
        );
    }

    /**
     * @test
     * @expectedException ChessServer\Exception\ParserException
     */
    public function validate_metadata_foo()
    {
        self::$parser->validate('/metadata foo');
    }
}
