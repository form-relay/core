<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Utility\GeneralUtility;

class GeneralContentResolver extends ContentResolver implements GeneralConfigurationResolverInterface
{
    protected $glue = '';

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    /**
     * @param string|FieldInterface|null $result
     * @param string|FieldInterface|null $content
     * @return string|FieldInterface|null
     */
    protected function add($result, $content)
    {
        if ($content !== null) {
            if ($result === null || $result === '') {
                $result = $content;
            } elseif ((string)$content !== '') {
                $result .= $this->glue ?: '';
                $result .= (string)$content;
            }
        }
        return $result;
    }

    public function build()
    {
        if (array_key_exists(static::KEYWORD_GLUE, $this->configuration)) {
            $glue = $this->resolveContent($this->configuration[static::KEYWORD_GLUE]);
            $this->glue = GeneralUtility::parseSeparatorString($glue);
            unset($this->configuration[static::KEYWORD_GLUE]);
        }

        $contentResolvers = [];
        foreach ($this->configuration as $key => $value) {
            $contentResolver = $this->resolveKeyword(is_numeric($key) ? 'general' : $key, $value);
            if ($contentResolver) {
                $contentResolvers[] = $contentResolver;
            }
        }

        $this->sortSubResolvers($contentResolvers);

        $result = null;
        foreach ($contentResolvers as $contentResolver) {
            $content = $contentResolver->build();
            $result = $this->add($result, $content);
        }
        foreach ($contentResolvers as $contentResolver) {
            if ($contentResolver->finish($result)) {
                break;
            }
        }
        return $result;
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
