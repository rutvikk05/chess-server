<?php

namespace ChessServer\Mode;

use ChessServer\AbstractMode;
use ChessServer\Command\Play;

class PvT extends AbstractMode
{
    /** player vs themselves */
    const NAME = 'pvt';
}
