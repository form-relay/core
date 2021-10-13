<?php

namespace FormRelay\Core\Factory;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Log\NullLogger;

class NullLoggerFactory implements LoggerFactoryInterface
{
    public function getLogger(string $forClass): LoggerInterface
    {
        return new NullLogger();
    }
}
