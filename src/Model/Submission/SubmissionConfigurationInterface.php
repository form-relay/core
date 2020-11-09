<?php

namespace FormRelay\Core\Model\Submission;

interface SubmissionConfigurationInterface
{
    const KEY_SELF = 'self';

    public function getDataProviderConfiguration(string $dataProviderName);
    public function getRoutePassCount(string $keyword): int;
    public function getRoutePassConfiguration(string $keyword, int $pass): array;
}
