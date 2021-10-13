<?php

namespace FormRelay\Core\Model\Submission;

use ArrayAccess;
use IteratorAggregate;

interface SubmissionDataInterface extends ArrayAccess, IteratorAggregate
{
    public function toArray(): array;

    public function fieldExists($key): bool;
    public function fieldEmpty($key): bool;
}
