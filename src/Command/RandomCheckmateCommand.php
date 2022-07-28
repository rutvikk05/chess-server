<?php

namespace ChessServer\Command;

use Chess\PGN\AN\Color;

class RandomCheckmateCommand extends AbstractCommand
{
    const TYPE_Q    = 'Q';

    const TYPE_R    = 'R';

    const TYPE_BB   = 'BB';

    const TYPE_BN   = 'BN';

    public function __construct()
    {
        $this->name = '/random_checkmate';
        $this->description = 'Starts a random checkmate position.';
        $this->params = [
            // mandatory param
            'turn' => '<string>',
            // mandatory param
            'items' => '<string>',
        ];
    }

    public function cases()
    {
        return [
            self::TYPE_Q,
            self::TYPE_R,
            self::TYPE_BB,
            self::TYPE_BN,
        ];
    }

    public function validate(array $argv)
    {
        isset($argv[1]) ? $turn = $argv[1] : $turn = null;
        isset($argv[2]) ? $items = json_decode(stripslashes($argv[2]), true) : $items = null;

        if ($turn !== Color::W && $turn !== Color::B) {
            return false;
        }

        if ($items) {
            $color = array_key_first($items);
            if ($color !== Color::W && $color !== Color::B) {
                return false;
            }
            $ids = current($items);
            if (!in_array($ids, self::cases())) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }
}
