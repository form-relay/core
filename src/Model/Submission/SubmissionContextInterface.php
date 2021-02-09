<?php

namespace FormRelay\Core\Model\Submission;

use ArrayAccess;

interface SubmissionContextInterface extends ArrayAccess
{
    public function toArray(): array;
}
