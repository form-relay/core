<?php

namespace FormRelay\Core\ConfigurationResolver\Context;

use ArrayAccess;
use FormRelay\Core\ConfigurationResolver\FieldTrackerInterface;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Model\Submission\SubmissionDataInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;

interface ConfigurationResolverContextInterface extends ArrayAccess
{
    public function copy(): ConfigurationResolverContextInterface;

    public function getFieldTracker(): FieldTrackerInterface;
    public function getSubmission(): SubmissionInterface;
    public function getData(): SubmissionDataInterface;
    public function getConfiguration(): SubmissionConfigurationInterface;
}
