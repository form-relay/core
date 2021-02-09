<?php

namespace FormRelay\Core\Queue;

class QueueProcessor
{
    protected $queue;
    protected $worker;

    public function __construct(QueueInterface $queue, WorkerInterface $worker)
    {
        $this->queue = $queue;
        $this->worker = $worker;
    }

    public function processBatch(int $batchSize = 1): bool
    {
        $result = true;
        $batch = $this->queue->fetchPending(0, $batchSize);
        $this->queue->markListAsRunning($batch);
        foreach ($batch as $job) {
            try {
                $this->worker->doJob($job);
                $this->queue->markAsDone($job);
            } catch (QueueException $e) {
                $result = false;
                $this->queue->markAsFailed($job, $e->getMessage());
            }
        }
        return $result;
    }
}
