<?php

namespace FormRelay\Core\Helper;

trait RegisterableTrait
{
    abstract public static function getClassType(): string;

    public static function getKeyword(): string
    {
        $namespaceParts = explode('\\', static::class);
        $class = array_pop($namespaceParts);
        $matches = [];
        if (preg_match('/^(.*)' . static::getClassType() . '$/', $class, $matches)) {
            return lcfirst($matches[1]);
        }
        return '';
    }

    public function getWeight(): int
    {
        return 10;
    }
}
