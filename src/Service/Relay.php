<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\DataProvider\DataProviderInterface;
use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Factory\QueueDataFactoryInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Queue\JobInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Queue\QueueException;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Queue\WorkerInterface;
use FormRelay\Core\Route\RouteInterface;

class Relay implements RelayInterface, WorkerInterface
{
    const KEY_DISABLE_STORAGE = 'disableStorage';
    const DEFAULT_DISABLE_STORAGE = true;

    const KEY_ASYNC = 'async';
    const DEFAULT_ASYNC = false;

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var QueueInterface $persistentQueue */
    protected $persistentQueue;

    /** @var QueueInterface $temporaryQueue */
    protected $temporaryQueue;

    /** @var QueueDataFactoryInterface */
    protected $queueDataFactory;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var array $enrichedSubmissionCache */
    protected $enrichedSubmissionCache = [];

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->persistentQueue = $registry->getQueue();
        $this->temporaryQueue = $registry->getTemporaryQueue();
        $this->queueDataFactory = $registry->getQueueDataFactory();
        $this->logger = $registry->getLogger(static::class);
    }

    protected function addContext(SubmissionInterface $submission)
    {
        $request = $this->registry->getRequest();

        $dataProviders = $this->registry->getDataProviders();
        /** @var DataProviderInterface $dataProvider */
        foreach ($dataProviders as $dataProvider) {
            try {
                $dataProvider->addContext($submission, $request);
            } catch (FormRelayException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $routes = $this->registry->getRoutes();
        /** @var RouteInterface $route */
        foreach ($routes as $route) {
            try {
                $passCount = $route->getPassCount($submission);
                for ($pass = 0; $pass < $passCount; $pass++) {
                    $route->addContext($submission, $request, $pass);
                }
            } catch (FormRelayException $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    protected function processDataProviders(SubmissionInterface $submission): SubmissionInterface
    {
        $cacheKey = $this->queueDataFactory->getSubmissionCacheKey($submission);
        if (isset($this->enrichedSubmissionCache[$cacheKey])) {
            return $this->enrichedSubmissionCache[$cacheKey];
        }

        $dataProviders = $this->registry->getDataProviders();
        /** @var DataProviderInterface $dataProvider */
        foreach ($dataProviders as $dataProvider) {
            $dataProvider->addData($submission);
        }
        $this->enrichedSubmissionCache[$cacheKey] = $submission;
        return $submission;
    }

    public function processJob(JobInterface $job): bool
    {
        try {
            $submission = $this->queueDataFactory->convertJobToSubmission($job);
            $submission = $this->processDataProviders($submission);

            $routeName = $this->queueDataFactory->getJobRoute($job);
            $pass = $this->queueDataFactory->getJobRoutePass($job);

            /** @var RouteInterface|null $route */
            $route = $this->registry->getRoute($routeName);
            if (!$route) {
                throw new FormRelayException('route "' . $routeName . '" not found');
            }

            return $route->processPass($submission, $pass);

        } catch (FormRelayException $e) {
            $this->logger->error($e->getMessage());
            throw new QueueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function process(SubmissionInterface $submission)
    {
        $this->addContext($submission);

        $syncPersistentJobs = [];
        $syncTemporaryJobs = [];
        $routes = $this->registry->getRoutes();
        /**
         * @var string $routeName
         * @var RouteInterface $route
         */
        foreach ($routes as $routeName => $route) {
            $passCount = $route->getPassCount($submission);
            for ($pass = 0; $pass < $passCount; $pass++) {
                if (!$route->passEnabled($submission, $pass)) {
                    continue;
                }

                $async = $submission->getConfiguration()->getWithRoutePassOverride(static::KEY_ASYNC, $routeName, $pass, static::DEFAULT_ASYNC);
                $disableStorage = $submission->getConfiguration()->getWithRoutePassOverride(static::KEY_DISABLE_STORAGE, $routeName, $pass, static::DEFAULT_DISABLE_STORAGE);

                if ($disableStorage && $async) {
                    $this->logger->error('Async submissions without storage are not possible. Using sync submission instead.');
                    $async = false;
                }

                $status = $async ? QueueInterface::STATUS_PENDING : QueueInterface::STATUS_RUNNING;
                $queue = $disableStorage ? $this->temporaryQueue : $this->persistentQueue;

                $job = $this->queueDataFactory->convertSubmissionToJob($submission, $routeName, $pass, $status);
                $queue->addJob($job);
                if (!$async) {
                    if ($disableStorage) {
                        $syncTemporaryJobs[] = $job;
                    } else {
                        $syncPersistentJobs[] = $job;
                    }
                }
            }
        }

        if (!empty($syncTemporaryJobs)) {
            $processor = $this->registry->getQueueProcessor($this->temporaryQueue, $this);
            $processor->processJobs($syncTemporaryJobs);
        }

        if (!empty($syncPersistentJobs)) {
            $processor = $this->registry->getQueueProcessor($this->persistentQueue, $this);
            $processor->processJobs($syncPersistentJobs);
        }
    }
}
