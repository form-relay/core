<?php

namespace FormRelay\Core\Queue;

interface WorkerInterface
{
    /**
     * @param JobInterface $job
     * @throws QueueException
     */
    public function doJob(JobInterface $job);
}
