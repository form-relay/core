<?php

namespace FormRelay\Core\DataDispatcher;

use FormRelay\Core\Helper\RegisterableTrait;
use FormRelay\Core\Log\LoggerInterface;

abstract class DataDispatcher implements DataDispatcherInterface
{
    use RegisterableTrait;

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getClassType(): string
    {
        return 'DataDispatcher';
    }
}
