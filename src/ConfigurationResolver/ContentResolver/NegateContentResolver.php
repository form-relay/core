<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class NegateContentResolver extends ContentResolver
{
    const KEY_TRUE = 'true';
    const DEFAULT_TRUE = '1';

    const KEY_FALSE = 'false';
    const DEFAULT_FALSE = '0';

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    protected function negateValue($value, $true, $false)
    {
        if ($value === $true) {
            return $false;
        } elseif ($value === $false) {
            return $true;
        } else {
            return (bool)$value ? $false : $true;
        }
    }

    public function finish(&$result): bool
    {
        $enabled = $this->configuration[SubmissionConfigurationInterface::KEY_SELF] ?? true;
        if ($enabled && $result !== null) {
            $true = $this->resolveContent($this->getConfig(static::KEY_TRUE)) ?? static::DEFAULT_TRUE;
            $false = $this->resolveContent($this->getConfig(static::KEY_FALSE)) ?? static::DEFAULT_FALSE;
            if ($result instanceof MultiValueField) {
                foreach ($result as $key => $value) {
                    $result[$key] = $this->negateValue($value, $true, $false);
                }
            } else {
                $result = $this->negateValue($result, $true, $false);
            }
        }
        return false;
    }

    public function getWeight(): int
    {
        return 101;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_TRUE => static::DEFAULT_TRUE,
            static::KEY_FALSE => static::DEFAULT_FALSE,
        ];
    }
}
