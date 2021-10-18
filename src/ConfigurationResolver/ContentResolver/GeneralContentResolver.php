<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Utility\GeneralUtility;

class GeneralContentResolver extends AbstractWrapperContentResolver implements GeneralConfigurationResolverInterface
{
    protected $glue = '';

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    /**
     * @param string|FieldInterface|null $result
     * @param string|FieldInterface|null $content
     * @return bool flag whether or not the build process should continue
     */
    protected function add(&$result, $content): bool
    {
        if ($content !== null) {
            if ($result === null || $result === '') {
                $result = $content;
            } elseif ((string)$content !== '') {
                $result .= $this->glue ?: '';
                if ($content instanceof MultiValueField && $this->glue) {
                    $content->setGlue($this->glue);
                }
                $result .= (string)$content;
            }
        }
        return true;
    }

    protected function preprocessConfiguration(): array
    {
        $config = $this->configuration;
        if (array_key_exists(static::KEYWORD_GLUE, $config)) {
            $this->glue = GeneralUtility::parseSeparatorString($config[static::KEYWORD_GLUE]);
            unset($config[static::KEYWORD_GLUE]);
        }
        return $config;
    }

    /**
     * @return FieldInterface|string|null
     */
    public function resolve()
    {
        $result = $this->build();
        $this->finish($result);
        return $result;
    }
}
