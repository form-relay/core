<?php

namespace FormRelay\Core\Model\Submission;

class Submission implements SubmissionInterface
{
    protected $data;
    protected $configuration;

    /**
     * Submission constructor.
     * @param array $data The form fields and their values as associative array
     * @param array $configurationList An array of (override) configurations
     */
    public function __construct(array $data, array $configurationList = [])
    {
        $this->data = new SubmissionData($data);
        $this->configuration = new SubmissionConfiguration($configurationList);
    }

    public function getData(): SubmissionDataInterface
    {
        return $this->data;
    }

    public function getConfiguration(): SubmissionConfigurationInterface
    {
        return $this->configuration;
    }
}
