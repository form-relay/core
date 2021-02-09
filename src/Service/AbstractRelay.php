<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\DataProvider\DataProviderInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;

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
        $dataProviders = $this->registry->getDataProviders();
        /** @var DataProviderInterface $dataProvider */
        foreach ($dataProviders as $dataProvider) {
            $dataProvider->addContext($submission);
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
}
