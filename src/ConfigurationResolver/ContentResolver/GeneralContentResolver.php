<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Utility\GeneralUtility;

class GeneralContentResolver extends ContentResolver implements GeneralConfigurationResolverInterface
{
    protected $glue = '';

    protected function add($result, $content): string
    {
        return $result
            . ($content && $result && $this->glue ? $this->glue : '')
            . $content;
    }

    public function resolve(): string
    {
        $result = $this->build();
        $this->finish($result);
        return $result;
    }

    public function build(): string
    {
        if (!is_array($this->config)) {
            $this->config = [SubmissionConfigurationInterface::KEY_CONTENT => $this->config];
        }

        $contentResolvers = [];
        foreach ($this->config as $key => $value) {
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

        $result = '';
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
