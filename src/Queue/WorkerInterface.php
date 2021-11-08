<?php

namespace FormRelay\Core\Queue;

use FormRelay\Core\Model\Queue\JobInterface;

interface WorkerInterface
{
    /**
     * @param JobInterface $job
     * @return bool
     * @throws QueueException
     */
    public function processJob(JobInterface $job): bool;
}
