<?php

namespace FormRelay\Core\Tests\Spy\Route;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Route\Route;
use FormRelay\Core\Service\PluginRegistryInterface;

class SpiedOnGenericRoute extends Route
{
    public $routeSpy = null;

    public function __construct(string $keyword, PluginRegistryInterface $registry, LoggerInterface $logger, RouteSpyInterface $routeSpy)
    {
        parent::__construct($keyword, $registry, $logger);
        $this->routeSpy = $routeSpy;
    }

    public function addContext(SubmissionInterface $submission, RequestInterface $request, int $pass)
    {
        $this->routeSpy->addContext($submission, $request, $pass);
    }

    protected function getDispatcher()
    {
        return $this->routeSpy;
    }
}
