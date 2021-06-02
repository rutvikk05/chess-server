<?php

namespace ChessServer;

abstract class AbstractCommand
{
    protected $name;

    protected $description;

    protected $params;

    protected $dependsOn;

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    abstract public function validate(array $command);
}
