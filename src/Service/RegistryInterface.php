<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\Factory\QueueDataFactoryInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Queue\QueueProcessorInterface;
use FormRelay\Core\Queue\WorkerInterface;
use FormRelay\Core\Request\RequestInterface;

interface RegistryInterface extends ClassRegistryInterface
{
    public function getLogger(string $forClass): LoggerInterface;
    public function getRequest(): RequestInterface;
    public function getQueue(): QueueInterface;
    public function getTemporaryQueue(): QueueInterface;
    public function getQueueDataFactory(): QueueDataFactoryInterface;
    public function getQueueProcessor(QueueInterface $queue, WorkerInterface $worker): QueueProcessorInterface;

    public function getDataProviderDefaultConfigurations(): array;
    public function getRouteDefaultConfigurations(): array;
    public function getDefaultConfiguration(): array;
}
