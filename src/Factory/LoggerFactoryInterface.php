<?php

namespace FormRelay\Core\Factory;

use FormRelay\Core\Log\LoggerInterface;

interface LoggerFactoryInterface
{
    public function getLogger(string $forClass): LoggerInterface;
}
