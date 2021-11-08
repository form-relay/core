<?php

namespace FormRelay\Core\DataDispatcher;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Service\RegisterableInterface;

interface DataDispatcherInterface extends RegisterableInterface
{
    /**
     * @param array $data
     * @throws FormRelayException
     */
    public function send(array $data);
}
