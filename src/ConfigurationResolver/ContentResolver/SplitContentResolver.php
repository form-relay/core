<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class SplitContentResolver extends ContentResolver
{
    const KEY_TOKEN = 'token';
    const DEFAULT_TOKEN = '\\s';

    const KEY_SLICE = 'slice';
    const DEFAULT_SLICE = null;

    const KEY_INDEX = 'index';
    const DEFAULT_INDEX = null;

    public function finish(&$result): bool
    {
        if ($result !== null) {
            if (!is_array($this->configuration)) {
                $this->configuration = [static::KEY_SLICE => $this->configuration];
            }
            $token = GeneralUtility::parseSeparatorString($this->getConfig(static::KEY_TOKEN));
            $slice = $this->getConfig(static::KEY_SLICE) ?? $this->getConfig(static::KEY_INDEX) ?? '';
            $indices = explode(':', $slice);

            $offset = $indices[0] ?: 1;
            if ($offset > 0) {
                $offset--;
            }
            if (count($indices) === 1) {
                // '' || 'X'
                $length = 1;
            } else {
                // 'X:' || ':Y' || 'X:Y'
                $length = $indices[1] ?: null;
            }
            $parts = explode($token, $result);
            if ($length === null) {
                $slices = array_slice($parts, $offset);
            } else {
                $slices = array_slice($parts, $offset, $length);
            }
            $result = implode($token, $slices);
        }
        return false;
    }

    public function getWeight(): int
    {
        return 15;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_TOKEN => static::DEFAULT_TOKEN,
            static::KEY_SLICE => static::DEFAULT_SLICE,
            static::KEY_INDEX => static::DEFAULT_INDEX,
        ];
    }
}
