<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Factory\QueueDataFactoryInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Route\RouteInterface;

interface RegistryInterface extends ClassRegistryInterface
{
    public function getLogger(string $forClass): LoggerInterface;
    public function getRequest(): RequestInterface;
    public function getQueue(): QueueInterface;
    public function getQueueDataFactory(): QueueDataFactoryInterface;

    public function getDataProviderDefaultConfigurations(): array;
    public function getRouteDefaultConfigurations(): array;
    public function getDefaultConfiguration(): array;
}
