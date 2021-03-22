<?php

namespace FormRelay\Core\Utility;

final class GeneralUtility
{
    const CHARACTER_MAP = [
        '\\n' => PHP_EOL,
        '\\s' => ' ',
        '\\t' => '  ',
    ];

    public static function isEmpty($value)
    {
        return strlen((string)$value) === 0;
    }

    public static function isTrue($value)
    {
        return !!$value;
    }

    public static function isFalse($value)
    {
        return !$value;
    }

    public static function parseSeparatorString($str)
    {
        $str = trim($str);
        foreach (static::CHARACTER_MAP as $key => $value) {
            $str = str_replace($key, $value, $str);
        }
        return $str;
    }

    public static function camel2dashed(string $className) {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $className));
    }
}
