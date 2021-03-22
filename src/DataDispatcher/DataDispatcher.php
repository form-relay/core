<?php

namespace FormRelay\Core\DataDispatcher;

use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Service\RegisterableTrait;

abstract class DataDispatcher implements DataDispatcherInterface
{
    use RegisterableTrait;

    protected $registry;
    protected $logger;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->logger = $registry->getLogger(static::class);
    }

    public static function getClassType(): string
    {
        return 'DataDispatcher';
    }
}
