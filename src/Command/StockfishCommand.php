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
        isset($argv[1]) ? $options = json_decode(stripslashes($argv[1]), true) : $options = null;
        isset($argv[2]) ? $params = json_decode(stripslashes($argv[2]), true) : $params = null;

        if ($options) {
            foreach ($options as $key => $val) {
                if (
                    !in_array($key, array_keys($this->params['options'])) ||
                    !is_int($val)
                ) {
                    return false;
                }
            }
        } else {
            return false;
        }

        if ($params) {
            foreach ($params as $key => $val) {
                if (
                    !in_array($key, array_keys($this->params['params'])) ||
                    !is_int($val)
                ) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }
}
