<?php

namespace FormRelay\Core\Tests\Spy\Route;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Route\Route;
use FormRelay\Core\Service\ClassRegistryInterface;

class SpiedOnGenericRoute extends Route
{
    public $routeSpy = null;

    public function __construct(ClassRegistryInterface $registry, LoggerInterface $logger, RouteSpyInterface $routeSpy)
    {
        parent::__construct($registry, $logger);
        $this->routeSpy = $routeSpy;
    }

    public static function getKeyword(): string
    {
        return 'generic';
    }

    public function addContext(SubmissionInterface $submission, RequestInterface $request)
    {
        $this->routeSpy->addContext($submission, $request);
    }

    protected function getDispatcher()
    {
        return $this->routeSpy;
    }
}
