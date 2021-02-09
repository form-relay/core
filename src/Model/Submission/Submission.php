<?php

namespace FormRelay\Core\Model\Submission;

class Submission implements SubmissionInterface
{
    protected $data;
    protected $configuration;
    protected $context;

    /**
     * Submission constructor.
     * @param array $data The form fields and their values as associative array
     * @param array $configurationList An array of (override) configurations
     * @param array $context The context needed for processing the submission
     */
    public function __construct(array $data, array $configurationList = [], array $context = [])
    {
        $this->data = new SubmissionData($data);
        $this->configuration = new SubmissionConfiguration($configurationList);
        $this->context = new SubmissionContext($context);
    }

    public function getData(): SubmissionDataInterface
    {
        return $this->data;
    }

    public function getConfiguration(): SubmissionConfigurationInterface
    {
        return $this->configuration;
    }

    public function getContext(): SubmissionContextInterface
    {
        return $this->context;
    }
}
