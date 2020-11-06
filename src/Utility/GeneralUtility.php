<?php

namespace FormRelay\Core\Utility;

final class GeneralUtility
{
    public static function parseSeparatorString($str)
    {
        $str = str_replace('\\n', PHP_EOL, trim($str));
        $str = str_replace('\\s', ' ', $str);
        return $str;
    }

    public static function camel2dashed(string $className) {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $className));
    }
}
