<?php

namespace FormRelay\Core\Model\Submission;

use FormRelay\Core\Utility\ConfigurationUtility;

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

    protected function getMergedConfiguration(bool $resolveNull = true): array
    {
        $result = [];
        foreach ($this->configurationList as $configuration) {
            $result = ConfigurationUtility::mergeConfiguration($result, $configuration, false);
        }
        if ($resolveNull) {
            $result = ConfigurationUtility::resolveNullInMergedConfiguration($result);
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

    public function dataProviderExists(string $dataProviderName): bool
    {
        return isset($this->get('dataProviders', [])[$dataProviderName]);
    }

    protected function getRouteConfiguration(string $routeName): array
    {
        $rawConfiguration = $this->getMergedConfiguration(false)['routes'][$routeName] ?? [];

        $baseConfiguration = $rawConfiguration;
        unset($baseConfiguration['passes']);

        $passConfigurations = [[]];
        if (isset($rawConfiguration['passes']) && $rawConfiguration['passes']) {
            $passConfigurations = $rawConfiguration['passes'];
        }

        $configuration = [];
        foreach ($passConfigurations as $key => $passConfiguration) {
            $passBaseConfiguration = $baseConfiguration;
            $configuration[$key] = ConfigurationUtility::mergeConfiguration($passBaseConfiguration, $passConfiguration, false);
        }
        $configuration = ConfigurationUtility::resolveNullInMergedConfiguration($configuration);
        ksort($configuration, SORT_NUMERIC);
        return $configuration;
    }

    protected function getRoutePassName(string $routeName, int $pass): string
    {
        $keys = array_keys($this->getRouteConfiguration($routeName));
        if (!isset($keys[$pass]) || is_numeric($keys[$pass])) {
            return $pass + 1;
        }
        return $keys[$pass];
    }

    public function getRoutePassLabel(string $routeName, int $pass): string
    {
        $label = $routeName;
        $passName = $this->getRoutePassName($routeName, $pass);
        if (!is_numeric($passName) || $this->getRoutePassCount($routeName) > 1) {
            $label .= '#' . $passName;
        }
        return $label;
    }

    public function getRoutePassCount(string $routeName): int
    {
        if (!$this->routeExists($routeName)) {
            return 0;
        }
        $configuration = $this->getRouteConfiguration($routeName);
        return count($configuration);
    }

    public function getRoutePassConfiguration(string $routeName, int $pass): array
    {
        $configuration = array_values($this->getRouteConfiguration($routeName));
        return $configuration[$pass];
    }

    public function routeExists(string $routeName): bool
    {
        return isset($this->getMergedConfiguration()['routes'][$routeName]);
    }

    public function routePassExists(string $routeName, int $pass): bool
    {
        return $pass >= 0 && $this->getRoutePassCount($routeName) > $pass;
    }
}
