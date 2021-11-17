<?php

namespace FormRelay\Core\DataDispatcher;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Plugin\PluginInterface;

interface DataDispatcherInterface extends PluginInterface
{
    /**
     * @param array $data
     * @throws FormRelayException
     */
    public function send(array $data);
}
