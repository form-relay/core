<?php

namespace FormRelay\Core\Queue;

use DateTime;
use FormRelay\Core\Model\Queue\SubmissionJob;

class NonPersistentQueue implements QueueInterface
{
    protected $queue = [];
    protected $index = 1;

    public function fetchWhere(array $status = [], int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0, int $minAgeInSeconds = 0)
    {
        $result = [];
        $now = new DateTime();
        $count = 0;
        /** @var JobInterface $job */
        foreach ($this->queue as $job) {
            if (!empty($status) && !in_array($job->getStatus(), $status)) {
                continue;
            }
            if ($minTimeSinceChangedInSeconds > 0 && $now->getTimestamp() - $job->getChanged()->getTimestamp() < $minTimeSinceChangedInSeconds) {
                continue;
            }
            if ($minAgeInSeconds > 0 && $now->getTimestamp() - $job->getCreated()->getTimestamp() < $minAgeInSeconds) {
                continue;
            }
            $count++;
            if ($count > $offset) {
                $result[] = $job;
            }
            if ($limit > 0 && ($count - $offset) >= $limit) {
                break;
            }
        }
        return $result;
    }

    public function fetch(array $status = [], int $limit = 0, int $offset = 0)
    {
        return $this->fetchWhere($status, $limit, $offset);
    }

    public function fetchPending(int $limit = 0, int $offset = 0)
    {
        return $this->fetchWhere([QueueInterface::STATUS_PENDING], $limit, $offset);
    }

    public function fetchRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0)
    {
        return $this->fetchWhere([QueueInterface::STATUS_RUNNING], $limit, $offset, $minTimeSinceChangedInSeconds);
    }

    public function fetchDone(int $limit = 0, int $offset = 0)
    {
        return $this->fetchWhere([QueueInterface::STATUS_DONE], $limit, $offset);
    }

    public function fetchFailed(int $limit = 0, int $offset = 0)
    {
        return $this->fetchWhere([QueueInterface::STATUS_FAILED], $limit, $offset);
    }

    public function markAs(JobInterface $job, int $status, string $message = '')
    {
        $job->setStatus($status);
        $job->setChanged(new DateTime());
        $job->setStatusMessage($message);
    }

    public function markAsPending(JobInterface $job)
    {
        $this->markAs($job, QueueInterface::STATUS_PENDING);
    }

    public function markAsRunning(JobInterface $job)
    {
        $this->markAs($job, QueueInterface::STATUS_RUNNING);
    }

    public function markAsDone(JobInterface $job)
    {
        $this->markAs($job, QueueInterface::STATUS_DONE);
    }

    public function markAsFailed(JobInterface $job, string $message = '')
    {
        $this->markAs($job, QueueInterface::STATUS_FAILED, $message);
    }

    public function markListAsRunning(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->markAsRunning($job);
        }
    }

    public function markListAsDone(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->markAsDone($job);
        }
    }

    public function markListAsFailed(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->markAsFailed($job);
        }
    }

    public function addJob(array $data, $status = QueueInterface::STATUS_PENDING)
    {
        $job = new SubmissionJob();
        $job->setId($this->index++);
        $job->setData($data);
        $job->setStatus($status);
        $this->queue[] = $job;
    }

    public function removeJob(JobInterface $job)
    {
        $this->queue = array_filter(
            $this->queue,
            function ($a) use ($job) { return $a !== $job; }
        );
    }

    public function removeOldJobs(int $minAgeInSeconds, array $status = [])
    {
        $jobs = $this->fetchWhere($status, 0, 0, 0, $minAgeInSeconds);
        foreach ($jobs as $job) {
            $this->removeJob($job);
        }
    }
}
