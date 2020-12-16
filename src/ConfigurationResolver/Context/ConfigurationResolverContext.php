<?php

namespace FormRelay\Core\ConfigurationResolver\Context;

use ArrayObject;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\ProcessedFieldsTracker;
use FormRelay\Core\ConfigurationResolver\ProcessedFieldsTrackerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;

class ConfigurationResolverContext extends ArrayObject implements ConfigurationResolverContextInterface
{
    protected $fieldsTracker;
    protected $submission;

    public function __construct(SubmissionInterface $submission, array $context = [], ProcessedFieldsTrackerInterface $fieldsTracker = null)
    {
        if ($fieldsTracker === null) {
            $fieldsTracker = new ProcessedFieldsTracker();
        }
        $this->fieldsTracker = $fieldsTracker;
        $this->submission = $submission;
        parent::__construct($context);
    }

    public function offsetGet($index)
    {
        switch ($index) {
            case 'submission':
                return $this->submission;
            case 'data':
                return $this->submission->getData();
            case 'config':
                return $this->submission->getConfiguration();
            case 'tracker':
                return $this->fieldsTracker;
            default:
                return parent::offsetGet($index);
        }
    }

    public function offsetExists($index)
    {
        switch ($index) {
            case 'submission':
                return true;
            case 'data':
                return true;
            case 'config':
                return true;
            case 'tracker':
                return true;
            default:
                return parent::offsetExists($index);
        }
    }

    public function copy(): ConfigurationResolverContextInterface
    {
        return new ConfigurationResolverContext($this->submission, iterator_to_array($this), $this->fieldsTracker);
    }
}
