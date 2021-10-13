<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\DataProvider\DataProviderInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Queue\JobInterface;
use FormRelay\Core\Route\RouteInterface;

abstract class AbstractRelay
{
    protected $registry;
    protected $logger;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->logger = $registry->getLogger(static::class);
    }

    protected function addContext(SubmissionInterface $submission)
    {
        $request = $this->registry->getRequest();
        $dataProviders = $this->registry->getDataProviders();
        /** @var DataProviderInterface $dataProvider */
        foreach ($dataProviders as $dataProvider) {
            $dataProvider->addContext($submission, $request);
        }
        $routes = $this->registry->getRoutes();
        /** @var RouteInterface $route */
        foreach ($routes as $route) {
            $route->addContext($submission, $request);
        }
    }

    protected function processDataProviders(SubmissionInterface $submission)
    {
        $dataProviders = $this->registry->getDataProviders();
        /** @var DataProviderInterface $dataProvider */
        foreach ($dataProviders as $dataProvider) {
            $dataProvider->addData($submission);
        }
    }

    protected function addJobToQueue(SubmissionInterface $submission, string $route, int $pass)
    {
        $submission->getContext()['job']['route'] = $route;
        $submission->getContext()['job']['pass'] = $pass;
        $jobData = $this->registry->getQueueDataFactory()->pack($submission);
        $this->registry->getQueue()->addJob($jobData);
    }

    protected function convertJobToSubmission(JobInterface $job): SubmissionInterface
    {
        return $this->registry->getQueueDataFactory()->unpack($job->getData());
    }

    protected function getJobRouteName(JobInterface $job): string
    {
        return $this->convertJobToSubmission($job)->getContext()['job']['route'] ?? '';
    }

    protected function getJobRoutePass(JobInterface $job): int
    {
        return $this->convertJobToSubmission($job)->getContext()['job']['pass'] ?? 0;
    }
}
