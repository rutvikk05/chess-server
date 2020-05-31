<?php

namespace PgnChessServer;

trait CommandTrait
{
    public static function printParams()
    {
        $r = preg_replace('/(\]\,)/i', ' ', json_encode(self::$params));
        $r = preg_replace('/:/i', ': ', $r);
        $r = preg_replace('/(\[|\]|\}|\{|\")/i', '', $r);

        return $r;
    }
}
