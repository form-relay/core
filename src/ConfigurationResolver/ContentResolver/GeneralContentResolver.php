<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
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
            } elseif ($content !== '') {
                $result .= $this->glue ?: '';
                if ($content instanceof MultiValueField && $this->glue) {
                    $result .= $content->__toString($this->glue);
                } else {
                    $result .= $content;
                }
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

    public function build()
    {
        $contentResolvers = [];
        foreach ($this->configuration as $key => $value) {
            if ($key === static::KEYWORD_GLUE) {
                $this->glue = GeneralUtility::parseSeparatorString($value);
                continue;
            }
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
}
