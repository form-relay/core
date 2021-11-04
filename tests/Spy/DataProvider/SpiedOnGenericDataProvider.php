<?php

namespace FormRelay\Core\Tests\Spy\DataProvider;

use FormRelay\Core\DataProvider\DataProvider;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\ClassRegistryInterface;

class SpiedOnGenericDataProvider extends DataProvider
{
    public $spy;

    public function __construct(ClassRegistryInterface $registry, LoggerInterface $logger, DataProviderSpyInterface $spy)
    {
        parent::__construct($registry, $logger);
        $this->spy = $spy;
    }

    public static function getKeyword(): string
    {
        return 'generic';
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
