<?php

namespace PgnChessServer\Tests\Unit\Command;

use PgnChessServer\Parser\CommandParser;
use PHPUnit\Framework\TestCase;

class MetadataTest extends TestCase
{
    /**
     * @test
     */
    public function validate_metadata()
    {
        $this->assertTrue(
            CommandParser::validate('/metadata')
        );
    }

    /**
     * @test
     */
    public function validate_metadata_foo()
    {
        $this->assertFalse(
            CommandParser::validate('/metadata foo')
        );
    }
}
