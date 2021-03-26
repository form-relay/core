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
            $configuration[] = ConfigurationUtility::mergeConfiguration($passBaseConfiguration, $passConfiguration, false);
        }
        $configuration = ConfigurationUtility::resolveNullInMergedConfiguration($configuration);
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
