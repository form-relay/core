<?php

namespace FormRelay\Core\Queue;

interface QueueInterface
{
    const STATUS_PENDING = 1;
    const STATUS_RUNNING = 2;
    const STATUS_DONE = 3;
    const STATUS_FAILED = 4;

    public function fetch(array $status = [], int $limit = 0, int $offset = 0);
    public function fetchPending(int $limit = 0, int $offset = 0);
    public function fetchRunning(int $limit = 0, int $offset = 0, int $minAgeInSeconds = 0);
    public function fetchDone(int $limit = 0, int $offset = 0);
    public function fetchFailed(int $limit = 0, int $offset = 0);

    public function markAsPending(JobInterface $job);
    public function markAsRunning(JobInterface $job);
    public function markAsDone(JobInterface $job);
    public function markAsFailed(JobInterface $job, string $message = '');

    public function markListAsRunning(array $jobs);
    public function markListAsDone(array $jobs);
    public function markListAsFailed(array $jobs);

    public function addJob(array $data, $status = self::STATUS_PENDING);
    public function removeJob(JobInterface $job);
}
