<?php

namespace FormRelay\Core\Tests\Spy\Route;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Route\Route;
use FormRelay\Core\Service\ClassRegistryInterface;

class SpiedOnGenericRoute extends Route
{
    public $dispatcher = null;

    public function __construct(ClassRegistryInterface $registry, LoggerInterface $logger, RouteSpyInterface $dataDispatcher)
    {
        parent::__construct($registry, $logger);
        $this->dispatcher = $dataDispatcher;
    }

    public static function getKeyword(): string
    {
        return 'generic';
    }

    protected function getDispatcher()
    {
        return $this->dispatcher;
    }
}
