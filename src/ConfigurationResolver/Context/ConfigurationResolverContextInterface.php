<?php

namespace FormRelay\Core\ConfigurationResolver\Context;

use ArrayAccess;
use FormRelay\Core\Model\Submission\SubmissionInterface;

interface ConfigurationResolverContextInterface extends ArrayAccess
{
    public function __construct(SubmissionInterface $submission, array $context = []);
    public function copy(): ConfigurationResolverContextInterface;
}
