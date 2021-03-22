<?php

namespace FormRelay\Core\Model\Submission;

use ArrayObject;
use FormRelay\Core\Utility\GeneralUtility;

class SubmissionData extends ArrayObject implements SubmissionDataInterface
{
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function fieldExists($key): bool
    {
        return array_key_exists($key, iterator_to_array($this));
    }

    public function fieldEmpty($key): bool
    {
        return !$this->fieldExists($key) || GeneralUtility::isEmpty($this[$key]);
    }
}
