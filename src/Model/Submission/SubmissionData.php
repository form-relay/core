<?php

namespace FormRelay\Core\Model\Submission;

use ArrayObject;

class SubmissionData extends ArrayObject implements SubmissionDataInterface
{
    public function __construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator")
    {
        parent::__construct($input, $flags, $iterator_class);
    }

    public function keyExists($key): bool
    {
        return array_key_exists($key, iterator_to_array($this));
    }

    public function fieldEmpty($key): bool
    {
        return !$this->keyExists($key) || empty($this[$key]);
    }


}
