<?php

namespace FormRelay\Core\Queue;

class QueueProcessor implements QueueProcessorInterface
{
    protected $queue;
    protected $worker;

    public function __construct(QueueInterface $queue, WorkerInterface $worker)
    {
        $this->queue = $queue;
        $this->worker = $worker;
    }

    public function processJobs(array $jobs)
    {
        if (!empty($jobs)) {
            $this->queue->markListAsRunning($jobs);
            foreach ($jobs as $job) {
                try {
                    $processed = $this->worker->processJob($job);
                    $this->queue->markAsDone($job, !$processed);
                } catch (QueueException $e) {
                    $this->queue->markAsFailed($job, $e->getMessage());
                }
            }
        }
    }

    public function processBatch(int $batchSize = 1)
    {
        $this->processJobs($this->queue->fetchPending($batchSize));
    }

    public function processAll()
    {
        $this->processJobs($this->queue->fetchPending());
    }
}
