<?php

namespace ChessServer\Parser;

use ChessServer\CommandContainer;
use ChessServer\Exception\ParserException;

class CommandParser
{
    protected $argv;

    protected $cli;

    public function __construct()
    {
        $this->cli = new CommandContainer;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function validate($string)
    {
        $this->argv = $this->filter($string);
        $command = $this->cli->findByName($this->argv[0]);
        if (!$command || !$command->validate($this->argv)) {
            throw new ParserException('Command not recognized by the server. Did you provide valid parameters?');
        }

        return $command;
    }

    protected function filter($string)
    {
        return array_map('trim', explode(' ', $string));
    }
}
