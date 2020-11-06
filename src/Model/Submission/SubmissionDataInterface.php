<?php

namespace FormRelay\Core\Model\Submission;

use ArrayAccess;

interface SubmissionDataInterface extends ArrayAccess
{
    public function keyExists($key): bool;
    public function fieldEmpty($key): bool;
}
