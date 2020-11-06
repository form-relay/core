<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\Model\Submission\SubmissionInterface;

class TimestampDataProvider extends DataProvider
{
    protected function process(SubmissionInterface $submission)
    {
        $this->setField(
            $submission,
            $this->getConfig('field', 'timestamp'),
            date($this->getConfig('format', 'c'))
        );
    }
}
