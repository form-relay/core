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
        $submission = $this->convertJobToSubmission($job);
        $routeName = $this->getJobRouteName($job);
        $pass = $this->getJobRoutePass($job);

        $this->processDataProviders($submission);

        try {
            /** @var RouteInterface $route */
            $route = $this->registry->getRoute($routeName);
            if (!$route) {
                throw new FormRelayException('route "' . $routeName . '" not found');
            }
            $route->processPass($submission, $pass);
        } catch (FormRelayException $e) {
            throw new QueueException($e->getMessage());
        }
    }
}
