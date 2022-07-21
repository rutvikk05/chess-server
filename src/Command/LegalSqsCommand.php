<?php

namespace ChessServer\Command;

class LegalSqsCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/legal_sqs';
        $this->description = 'Returns the legal squares of a piece.';
        $this->params = [
            'position' => '<string>',
        ];
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
