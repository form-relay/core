<?php

namespace FormRelay\Core\Tests\Integration;

use FormRelay\Core\Model\Submission\Submission;

trait SubmissionTestTrait //  extends \PHPUnit\Framework\TestCase
{
    /** @var array */
    protected $submissionData = [];

    /** @var array */
    protected $submissionConfiguration = [];

    /** @var array */
    protected $submissionContext = [];

    protected function baseConfiguration(): array
    {
        return [
            'async' => false,
            'dataProviders' => [],
            'routes' => [],
        ];
    }

    protected function initSubmission()
    {
        $this->submissionData = [];
        $this->submissionConfiguration = [$this->baseConfiguration()];
        $this->submissionContext = [];
    }

    protected function getSubmission()
    {
        return new Submission($this->submissionData, $this->submissionConfiguration, $this->submissionContext);
    }

    protected function setupDummyData(int $amount = 3) {
        $this->submissionData = [];
        for ($i = 1; $i <= $amount; $i++) {
            $this->submissionData['field' . $i] = 'value' . $i;
        }
    }

    protected function addRouteConfiguration(string $name, array $configuration, int $index = 0)
    {
        $this->submissionConfiguration[$index]['routes'][$name] = $configuration;
    }

    protected function addDataProviderConfiguration(string $name, array $configuration, int $index = 0)
    {
        $this->submissionConfiguration[$index]['dataProviders'][$name] = $configuration;
    }

    protected function setSubmissionAsync(bool $async = true, int $index = 0)
    {
        $this->submissionConfiguration[$index]['async'] = $async;
    }
}