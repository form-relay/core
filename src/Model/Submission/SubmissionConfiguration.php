<?php

namespace FormRelay\Core\Model\Submission;

class SubmissionConfiguration implements SubmissionConfigurationInterface
{
    protected $configurationList = [];

    public function __construct(array $configurationList)
    {
        $this->configurationList = $configurationList;
    }

    protected function mergeConfiguration(array $target, array $source, bool $resolveNull = true)
    {
        foreach ($source as $key => $value) {
            if (!isset($target[$key])) {
                if (!$resolveNull || $value !== null) {
                    $target[$key] = $value;
                }
            } elseif (is_array($value) && is_array($target[$key])) {
                $target[$key] = $this->mergeConfiguration($target[$key], $value);
            } elseif (is_array($value)) {
                if ($target[$key] === null) {
                    $target[$key] = $value;
                } else {
                    $target[$key] = $this->mergeConfiguration([static::KEY_CONTENT => $target[$key]], $value);
                }
            } elseif (is_array($target[$key])) {
                if ($value === null) {
                    if ($resolveNull) {
                        unset($target[$key]);
                    } else {
                        $target[$key] = $value;
                    }
                } else {
                    $target[$key] = $this->mergeConfiguration($target[$key], [static::KEY_CONTENT => $value]);
                }
            } else {
                if (!$resolveNull || $value === null) {
                    unset($target[$key]);
                } else {
                    $target[$key] = $value;
                }
            }
        }
        return $target;
    }

    protected function resolveNullInMergedConfiguration(array $configuration)
    {
        return $this->mergeConfiguration($configuration, $configuration, true);
    }

    protected function getMergedConfiguration(bool $resolveNull = true): array
    {
        $result = [];
        foreach ($this->configurationList as $configuration) {
            $result = $this->mergeConfiguration($result, $configuration, false);
        }
        if ($resolveNull) {
            $result = $this->resolveNullInMergedConfiguration($result);
        }
        return $result;
    }

    public function getDataProviderConfiguration(string $dataProviderName)
    {
        $configuration = $this->getMergedConfiguration();
        if (isset($configuration['providers'][$dataProviderName])) {
            return $configuration['providers'][$dataProviderName];
        }
        return [];
    }

    protected function getRouteConfiguration(string $routeName): array
    {
        $rawConfiguration = $this->getMergedConfiguration(false)['routes'][$routeName] ?? [];

        $baseConfiguration = $rawConfiguration;
        unset($baseConfiguration['passes']);

        $passConfigurations = isset($rawConfiguration['passes']) ? $rawConfiguration['passes'] : [[]];

        $configuration = [];
        foreach ($passConfigurations as $passConfiguration) {
            $passBaseConfiguration = $baseConfiguration;
            $configuration[] = $this->mergeConfiguration($passBaseConfiguration, $passConfiguration, false);
        }
        $configuration = $this->resolveNullInMergedConfiguration($configuration);
        return $configuration;
    }

    public function getRoutePassCount(string $keyword): int
    {
        $configuration = $this->getRouteConfiguration($keyword);
        return count($configuration);
    }

    public function getRoutePassConfiguration(string $keyword, int $pass): array
    {
        $configuration = $this->getRouteConfiguration($keyword);
        return $configuration[$pass];
    }
}
