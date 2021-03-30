<?php

namespace FormRelay\Core\Utility;

use FormRelay\Core\Model\Form\MultiValueField;

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

    public static function castValueToArray($value, $token = ',', $trim = true)
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof MultiValueField) {
            return $value->toArray();
        }

        $value = (string)$value;
        $array = !static::isEmpty($value) ? explode($token, $value) : [];

        if ($trim) {
            $array = array_map('trim', $array);
        }

        return $array;
    }
}
