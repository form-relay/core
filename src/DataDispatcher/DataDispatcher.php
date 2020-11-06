<?php

namespace FormRelay\Core\DataDispatcher;

use FormRelay\Core\Utility\GeneralUtility;

abstract class DataDispatcher implements DataDispatcherInterface
{
    public static function getKeyword(): string
    {
        $namespaceParts = explode('\\', static::class);
        $class = array_pop($namespaceParts);
        $matches = [];
        if (preg_match('/^(.*)DataDispatcher$/', $class, $matches)) {
            return GeneralUtility::camel2dashed($matches[1]);
        }
        return '';
    }

    public function getWeight(): int
    {
        return 10;
    }
}
