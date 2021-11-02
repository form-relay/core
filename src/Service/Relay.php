<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\DataProvider\DataProviderInterface;
use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Queue\JobInterface;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Route\RouteInterface;

class Relay implements RelayInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var QueueInterface $queue */
    protected $queue;

    /** @var LoggerInterface  */
    protected $logger;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->queue = $registry->getQueue();
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

    protected function addJobToQueue(SubmissionInterface $submission, string $route, int $pass, int $status = QueueInterface::STATUS_PENDING): JobInterface
    {
        $submission->getContext()->setInNamespace('job', 'route', $route);
        $submission->getContext()->setInNamespace('job', 'pass', $pass);
        $jobData = $this->registry->getQueueDataFactory()->pack($submission);
        return $this->queue->addJob($jobData, $status);
    }

    protected function convertJobToSubmission(JobInterface $job): SubmissionInterface
    {
        return $this->registry->getQueueDataFactory()->unpack($job->getData());
    }

    protected function getJobRouteName(JobInterface $job): string
    {
        return $this->convertJobToSubmission($job)->getContext()->getFromNamespace('job', 'route', '');;
    }

    protected function getJobRoutePass(JobInterface $job): int
    {
        return $this->convertJobToSubmission($job)->getContext()->getFromNamespace('job', 'pass', 0);
    }

    /**
     * @param SubmissionInterface $submission
     * @param string $routeName
     * @param int $pass
     * @throws FormRelayException
     */
    public function processRoutePass(SubmissionInterface $submission, string $routeName, int $pass)
    {
        /** @var RouteInterface|null $route */
        $route = $this->registry->getRoute($routeName);
        if (!$route) {
            throw new FormRelayException('route "' . $routeName . '" not found');
        }
        $route->processPass($submission, $pass);
    }

    public function processJob(JobInterface $job)
    {
        try {
            if ($job->getStatus() !== QueueInterface::STATUS_RUNNING) {
                $this->queue->markAsRunning($job);
            }
            $submission = $this->convertJobToSubmission($job);
            $this->processDataProviders($submission);
            $route = $this->getJobRouteName($job);
            $pass = $this->getJobRoutePass($job);
            $this->processRoutePass($submission, $route, $pass);
            $this->queue->markAsDone($job);
        } catch (FormRelayException $e) {
            $this->logger->error($e->getMessage());
            $this->queue->markAsFailed($job, $e->getMessage());
        }
    }

    public function processJobs(array $jobs)
    {
        if (!empty($jobs)) {
            $this->queue->markListAsRunning($jobs);
            foreach ($jobs as $job) {
                $this->processJob($job);
            }
        }
    }

    public function processFromQueue(int $batchSize)
    {
        $batch = $this->queue->fetchPending($batchSize);
        $this->processJobs($batch);
    }

    protected function processWithQueue(SubmissionInterface $submission)
    {
        $this->addContext($submission);
        $async = $submission->getConfiguration()->get('async', false);
        $initialStatus = $async ? QueueInterface::STATUS_PENDING : QueueInterface::STATUS_RUNNING;

        $jobs = [];
        $routes = $this->registry->getRoutes();
        /** @var RouteInterface $route */
        foreach ($routes as $route) {
            $passCount = $route->getPassCount($submission);
            for ($pass = 0; $pass < $passCount; $pass++) {
                $jobs[$route->getKeyword()][$pass] = $this->addJobToQueue($submission, $route->getKeyword(), $pass, $initialStatus);
            }
        }

        if (!$async) {
            try {
                $this->processDataProviders($submission);
                foreach ($jobs as $routeName => $jobsInRoute) {
                    foreach ($jobsInRoute as $pass => $job) {
                        try {
                            $this->processRoutePass($submission, $route, $pass);
                            $this->queue->markAsDone($job);
                        } catch (FormRelayException $e) {
                            $this->logger->error($e->getMessage());
                            $this->queue->markAsFailed($job, $e->getMessage());
                        }
                    }
                }
            } catch (FormRelayException $e) {
                $this->logger->error($e->getMessage());
                $this->queue->markListAsFailed($jobs, $e->getMessage());
            }
        }
    }

    protected function processWithoutQueue(SubmissionInterface $submission)
    {
        try {
            $this->addContext($submission);
            $this->processDataProviders($submission);

            $routes = $this->registry->getRoutes();
            /** @var RouteInterface $route */
            foreach ($routes as $route) {
                $passCount = $route->getPassCount($submission);
                for ($pass = 0; $pass < $passCount; $pass++) {
                    try {
                        $this->processRoutePass($submission, $route->getKeyword(), $pass);
                    } catch (FormRelayException $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            }
        } catch (FormRelayException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function process(SubmissionInterface $submission)
    {
        $async = $submission->getConfiguration()->get('async', false);
        $disableQueue = $submission->getConfiguration()->get('disableStorage', false);
        if ($disableQueue) {
            if ($async) {
                $this->logger->error('Async submissions without storage are not possible. Using sync submission instead.');
            }
            $this->processWithoutQueue($submission);
        } else {
            $this->processWithQueue($submission);
        }
    }
}
