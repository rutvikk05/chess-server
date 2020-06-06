<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Command\Metadata;
use PgnChessServer\Tests\Unit\CommandTestCase;

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
     * @expectedException PgnChessServer\Exception\ParserException
     */
    public function validate_metadata_foo()
    {
        self::$parser->validate('/metadata foo');
    }
}
