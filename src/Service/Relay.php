<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\DataProvider\DataProviderInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Route\RouteInterface;
use FormRelay\Core\Model\Submission\Submission;
use FormRelay\Core\Model\Submission\SubmissionInterface;

class Relay
{
    protected $registry;
    protected $logger;

    public function __construct(RegistryInterface $registry, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->logger = $logger;
    }

    protected function processDataProviders(SubmissionInterface $submission)
    {
        $dataProviders = $this->registry->getDataProviders();
        /** @var DataProviderInterface $dataProvider */
        foreach ($dataProviders as $dataProvider) {
            $dataProvider->addData($submission);
        }
    }

    protected function processRoutes(SubmissionInterface $submission)
    {
        $routes = $this->registry->getRoutes();
        $result = false;
        /** @var RouteInterface $route */
        foreach ($routes as $route) {
            $result = $route->process($submission) || $result;
        }
        return $result;
    }

    public function process(SubmissionInterface $submission, bool $simulate = false)
    {
        if (!$simulate) {
            $this->processDataProviders($submission);
        }
        return $this->processRoutes($submission);
    }
}
