<?php

namespace FormRelay\Core\Model\Submission;

use ArrayObject;

class SubmissionContext extends ArrayObject implements SubmissionContextInterface
{
    public function toArray(): array
    {
        return iterator_to_array($this);
    }
}
