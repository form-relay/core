<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class AbstractWrapperContentResolver extends ContentResolver
{
    /** @var array $subContentResolvers */
    protected $subContentResolvers;

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    protected function getGlue(): string
    {
        return '';
    }

    /**
     * @param string|FieldInterface|null $result
     * @param string|FieldInterface|null $content
     * @param mixed $key
     * @return bool flag whether or not the build process should continue
     */
    protected function add(&$result, $content, $key): bool
    {
        if ($content !== null) {
            if (GeneralUtility::isEmpty($result)) {
                $result = $content;
            } elseif (!GeneralUtility::isEmpty($content)) {
                $result .= $this->getGlue();
                $result .= (string)$content;
            }
        }
        return true;
    }

    protected function getSubContentResolver($key, $value)
    {
        return $this->resolveKeyword(is_numeric($key) ? 'general' : $key, $value);
    }

    protected function getSubContentResolvers(array $config): array
    {
        $contentResolvers = [];
        foreach ($config as $key => $value) {
            $contentResolver = $this->getSubContentResolver($key, $value);
            if ($contentResolver) {
                $contentResolvers[$key] = $contentResolver;
            }
        }
        $this->sortSubResolvers($contentResolvers);
        return $contentResolvers;
    }

    protected function getInitialValue()
    {
        return null;
    }

    protected function buildSubContent()
    {
        $result = $this->getInitialValue();
        foreach ($this->subContentResolvers as $key => $contentResolver) {
            $content = $contentResolver->build();
            if (!$this->add($result, $content, $key)) {
                break;
            }
        }
        return $result;
    }

    /**
     * @param string|FieldInterface|null $result
     */
    protected function finishSubContent(&$result)
    {
        foreach ($this->subContentResolvers as $contentResolver) {
            if ($contentResolver->finish($result)) {
                break;
            }
        }
    }

    protected function preprocessConfiguration(): array
    {
        return $this->configuration;
    }

    public function build()
    {
        $config = $this->preprocessConfiguration();
        $this->subContentResolvers = $this->getSubContentResolvers($config);
        $result = $this->buildSubContent();
        $this->finishSubContent($result);
        return $result;
    }
}
