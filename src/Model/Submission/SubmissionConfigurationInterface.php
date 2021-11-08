<?php

namespace FormRelay\Core\Model\Submission;

interface SubmissionConfigurationInterface
{
    const KEY_SELF = 'self';

    public function toArray(): array;

    public function get(string $key, $default = null);
    public function getWithRoutePassOverride(string $key, string $route, int $pass, $default = null);

    public function dataProviderExists(string $dataProviderName): bool;
    public function getDataProviderConfiguration(string $dataProviderName);

    public function routeExists(string $routeName): bool;
    public function routePassExists(string $routeName, int $pass): bool;
    public function getRoutePassCount(string $routeName): int;
    public function getRoutePassConfiguration(string $routeName, int $pass): array;
    public function getRoutePassLabel(string $routeName, int $pass): string;
}
