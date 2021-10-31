<?php

namespace FormRelay\Core\Tests\Spy\Route;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Route\Route;
use FormRelay\Core\Service\ClassRegistryInterface;

class SpiedOnRoute extends Route
{
    public $dispatcher = null;

    public function __construct(ClassRegistryInterface $registry, LoggerInterface $logger, RouteSpyInterface $dataDispatcher)
    {
        parent::__construct($registry, $logger);
        $this->dispatcher = $dataDispatcher;
    }

    protected function getDispatcher()
    {
        return $this->dispatcher;
    }
}
