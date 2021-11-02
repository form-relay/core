<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Queue\JobInterface;

interface RelayInterface
{
    public function processRoutePass(SubmissionInterface $submission, string $routeName, int $pass);
    public function processJob(JobInterface $job);
    public function processJobs(array $jobs);
    public function processFromQueue(int $batchSize);
    public function process(SubmissionInterface $submission);
}
