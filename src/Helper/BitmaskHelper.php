<?php

namespace RB\System\Helper;

class BitmaskHelper
{
    public static function setBit(int $bit, int $mask): int
    {
        return $mask | $bit;
    }

    public static function hasBit(int $bit, int $mask): bool
    {
        return ($mask & $bit) === $bit;
    }

    public static function unsetBit(int $bit, int $mask): int
    {
        return $mask & (~$bit);
    }
}