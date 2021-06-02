<?php

namespace ChessServer\Command;

use ChessServer\AbstractCommand;

class Help extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/help';
        $this->description = 'Provides information on the commands available.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }
}
