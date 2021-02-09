<?php

namespace FormRelay\Core\Model\Submission;

class SubmissionConfiguration implements SubmissionConfigurationInterface
{
    protected $configurationList = [];

    public function __construct(array $configurationList)
    {
        $this->configurationList = $configurationList;
    }

    public function addConfigurationOverride(array $configuration)
    {
        $this->configurationList[] = $configuration;
    }

    public function toArray(): array
    {
        return $this->configurationList;
    }

    protected function mergeConfiguration(array $target, array $source, bool $resolveNull = true)
    {
        foreach ($source as $key => $value) {
            if (!array_key_exists($key, $target)) {
                if (!$resolveNull || $value !== null) {
                    $target[$key] = $value;
                }
            } elseif (is_array($value) && is_array($target[$key])) {
                $target[$key] = $this->mergeConfiguration($target[$key], $value, $resolveNull);
            } elseif (is_array($value)) {
                if ($target[$key] === null) {
                    $target[$key] = $value;
                } else {
                    $target[$key] = $this->mergeConfiguration([static::KEY_SELF => $target[$key]], $value, $resolveNull);
                }
            } elseif (is_array($target[$key])) {
                if ($value === null) {
                    if ($resolveNull) {
                        unset($target[$key]);
                    } else {
                        $target[$key] = $value;
                    }
                } else {
                    $target[$key] = $this->mergeConfiguration($target[$key], [static::KEY_SELF => $value], $resolveNull);
                }
            } else {
                if ($resolveNull && $value === null) {
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

    public function get(string $key, $default = null)
    {
        $configuration = $this->getMergedConfiguration();
        if (isset($configuration[$key])) {
            return $configuration[$key];
        }
        return $default;
    }

    public function getDataProviderConfiguration(string $dataProviderName)
    {
        return $this->get('dataProviders', [])[$dataProviderName] ?? [];
    }

    protected function getRouteConfiguration(string $routeName): array
    {
        $rawConfiguration = $this->getMergedConfiguration(false)['routes'][$routeName] ?? [];

        $baseConfiguration = $rawConfiguration;
        unset($baseConfiguration['passes']);

        $passConfigurations = [[]];
        if (isset($rawConfiguration['passes']) && $rawConfiguration['passes']) {
            $passConfigurations = $rawConfiguration['passes'];
        };

        $configuration = [];
        foreach ($passConfigurations as $passConfiguration) {
            $passBaseConfiguration = $baseConfiguration;
            $configuration[] = $this->mergeConfiguration($passBaseConfiguration, $passConfiguration, false);
        }
        $configuration = $this->resolveNullInMergedConfiguration($configuration);
        return $configuration;
    }

    public function getRoutePassCount(string $routeName): int
    {
        $configuration = $this->getRouteConfiguration($routeName);
        return count($configuration);
    }

    public function getRoutePassConfiguration(string $routeName, int $pass): array
    {
        $configuration = $this->getRouteConfiguration($routeName);
        return $configuration[$pass];
    }
}
