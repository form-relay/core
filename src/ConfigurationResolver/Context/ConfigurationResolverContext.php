<?php

namespace FormRelay\Core\ConfigurationResolver\Context;

use ArrayObject;
use FormRelay\Core\ConfigurationResolver\FieldTracker;
use FormRelay\Core\ConfigurationResolver\FieldTrackerInterface;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Model\Submission\SubmissionDataInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;

class ConfigurationResolverContext extends ArrayObject implements ConfigurationResolverContextInterface
{
    protected $fieldTracker;
    protected $submission;

    public function __construct(SubmissionInterface $submission, array $context = [], FieldTrackerInterface $fieldTracker = null)
    {
        if ($fieldTracker === null) {
            $fieldTracker = new FieldTracker();
        }
        $this->fieldTracker = $fieldTracker;
        $this->submission = $submission;

        $context['submission'] = $submission;
        $context['data'] = $submission->getData();
        $context['config'] = $submission->getConfiguration();
        $context['tracker'] = $this->fieldTracker;

        parent::__construct($context);
    }

    public function getFieldTracker(): FieldTrackerInterface
    {
        return $this->fieldTracker;
    }

    public function getSubmission(): SubmissionInterface
    {
        return $this->submission;
    }

    public function getData(): SubmissionDataInterface
    {
        return $this->getSubmission()->getData();
    }

    public function getConfiguration(): SubmissionConfigurationInterface
    {
        return $this->getSubmission()->getConfiguration();
    }

    public function copy(): ConfigurationResolverContextInterface
    {
        return new ConfigurationResolverContext($this->submission, iterator_to_array($this), $this->fieldTracker);
    }
}
