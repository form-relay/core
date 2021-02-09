<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Queue\JobInterface;
use FormRelay\Core\Queue\QueueException;
use FormRelay\Core\Queue\WorkerInterface;
use FormRelay\Core\Route\RouteInterface;

class QueueWorker extends AbstractRelay implements WorkerInterface
{
    public function doJob(JobInterface $job)
    {
        $submission = $this->registry->getQueueDataFactory()->unpack($job->getData());
        $this->processDataProviders($submission);

        $context = $submission->getContext();
        $routeName = $context['job']['route'];
        $pass = $context['job']['pass'];

        try {
            /** @var RouteInterface $route */
            $route = $this->registry->getRoutes()[$routeName] ?? null;
            if (!$route) {
                throw new FormRelayException('route "' . $routeName . '" not found');
            }
            $route->processPass($submission, $pass);
        } catch (FormRelayException $e) {
            throw new QueueException($e->getMessage());
        }
    }
}
