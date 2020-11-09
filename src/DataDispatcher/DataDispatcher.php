<?php

namespace FormRelay\Core\DataDispatcher;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class DataDispatcher implements DataDispatcherInterface
{
    protected $registry;
    protected $logger;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->logger = $registry->getLogger(static::class);
    }

    public static function getKeyword(): string
    {
        $namespaceParts = explode('\\', static::class);
        $class = array_pop($namespaceParts);
        $matches = [];
        if (preg_match('/^(.*)DataDispatcher$/', $class, $matches)) {
            return GeneralUtility::camel2dashed($matches[1]);
        }
        return '';
    }

    public function getWeight(): int
    {
        return 10;
    }
}
