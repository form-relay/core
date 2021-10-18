<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\FieldInterface;

abstract class AbstractWrapperContentResolver extends ContentResolver
{
    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    /**
     * @param string|FieldInterface|null $result
     * @param string|FieldInterface|null $content
     * @return bool flag whether or not the build process should continue
     */
    abstract protected function add(&$result, $content): bool;

    protected function getSubContentResolvers(array $config): array
    {
        $contentResolvers = [];
        foreach ($config as $key => $value) {
            $contentResolver = $this->resolveKeyword(is_numeric($key) ? 'general' : $key, $value);
            if ($contentResolver) {
                $contentResolvers[] = $contentResolver;
            }
        }
        $this->sortSubResolvers($contentResolvers);
        return $contentResolvers;
    }

    protected function buildSubContent(array $contentResolvers)
    {
        $result = null;
        foreach ($contentResolvers as $contentResolver) {
            $content = $contentResolver->build();
            if (!$this->add($result, $content)) {
                break;
            }
        }
        return $result;
    }

    /**
     * @param string|FieldInterface|null $result
     * @param array $contentResolvers
     */
    protected function finishSubContent(&$result, array $contentResolvers)
    {
        foreach ($contentResolvers as $contentResolver) {
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
        $contentResolvers = $this->getSubContentResolvers($config);
        $result = $this->buildSubContent($contentResolvers);
        $this->finishSubContent($result, $contentResolvers);
        return $result;
    }
}
