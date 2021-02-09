<?php

namespace FormRelay\Core\Model\Submission;

use ArrayAccess;

interface SubmissionDataInterface extends ArrayAccess
{
    public function toArray(): array;

    public function fieldExists($key): bool;
    public function fieldEmpty($key): bool;
}
