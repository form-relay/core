<?php

namespace FormRelay\Core\DataDispatcher;

use FormRelay\Core\Service\RegisterableInterface;

interface DataDispatcherInterface extends RegisterableInterface
{
    public function send(array $data, array $configuration): bool;
}
