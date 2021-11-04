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
        return (bool)$value;
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

    public static function isList($value): bool
    {
        return is_array($value) || $value instanceof MultiValueField;
    }

    public static function castValueToArray($value, $token = ',', $trim = true): array
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

    public static function shortenHash(string $hash): string
    {
        if ($hash === 'undefined') {
            return 'undefined';
        }
        return substr($hash, 0, 5);
    }

    public static function calculateHash(array $submission, bool $short = false): string
    {
        if (empty($submission)) {
            return 'undefined';
        }
        if (isset($submission['context']['job'])) {
            unset($submission['context']['job']);
        }
        if (isset($submission['context']['submission'])) {
            unset($submission['context']['submission']);
        }
        $serialized = serialize($submission);
        if (!$serialized) {
            return 'undefined';
        }
        $hash = strtoupper(md5($serialized));
        return $short ? static::shortenHash($hash) : $hash;
    }
}
