<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\Model\Submission\SubmissionInterface;

interface RelayInterface
{
    public function process(SubmissionInterface $submission);
}
