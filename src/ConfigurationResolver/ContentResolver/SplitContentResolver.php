<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Utility\GeneralUtility;

class SplitContentResolver extends ContentResolver
{
    const KEY_TOKEN = 'token';
    const DEFAULT_TOKEN = '\\s';

    const KEY_SLICE = 'slice';
    const KEY_INDEX = 'index';

    public function finish(&$result): bool
    {
        if ($result !== null) {
            if (!is_array($this->config)) {
                $this->config = [static::KEY_SLICE => $this->config];
            }
            $token = GeneralUtility::parseSeparatorString($this->config[static::KEY_TOKEN] ?? static::DEFAULT_TOKEN);
            $slice = $this->config[static::KEY_SLICE] ?? $this->config[static::KEY_INDEX] ?? '';
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
}
