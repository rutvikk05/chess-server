<?php

namespace ChessServer\Command;

class StockfishCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/stockfish';
        $this->description = "Returns Stockfish's response to the current position.";
        $this->params = [
            // mandatory param
            'options' => [
                'Skill Level' => 'int',
            ],
            // mandatory param
            'params' => [
                'depth' => 'int',
            ],
        ];
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        $options = json_decode(stripslashes($argv[1]), true);
        $params = json_decode(stripslashes($argv[2]), true);
        foreach ($options as $key => $val) {
            if (
                !in_array($key, array_keys($this->params['options'])) ||
                !is_int($val)
            ) {
                return false;
            }
        }
        foreach ($params as $key => $val) {
            if (
                !in_array($key, array_keys($this->params['params'])) ||
                !is_int($val)
            ) {
                return false;
            }
        }

        return true;
    }
}
