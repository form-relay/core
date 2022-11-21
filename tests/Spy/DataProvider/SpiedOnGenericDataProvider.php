<?php

namespace FormRelay\Core\Tests\Spy\DataProvider;

use FormRelay\Core\DataProvider\DataProvider;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\PluginRegistryInterface;

class SpiedOnGenericDataProvider extends DataProvider
{
    public $spy;

    public function __construct(string $keyword, PluginRegistryInterface $registry, LoggerInterface $logger, DataProviderSpyInterface $spy)
    {
        parent::__construct($keyword, $registry, $logger);
        $this->spy = $spy;
    }

    protected function processContext(SubmissionInterface $submission, RequestInterface $request)
    {
        $this->spy->processContext($submission, $request);
    }

    protected function process(SubmissionInterface $submission)
    {
        $this->spy->process($submission);
    }
}
