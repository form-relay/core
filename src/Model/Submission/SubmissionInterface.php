<?php

namespace FormRelay\Core\Model\Submission;

interface SubmissionInterface
{
    public function getData(): SubmissionDataInterface;
    public function getConfiguration(): SubmissionConfigurationInterface;
    public function getContext(): SubmissionContextInterface;
}
