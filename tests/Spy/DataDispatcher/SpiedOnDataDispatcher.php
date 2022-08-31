<?php

namespace FormRelay\Core\Tests\Spy\DataDispatcher;

use FormRelay\Core\DataDispatcher\DataDispatcher;
use FormRelay\Core\Log\LoggerInterface;

class SpiedOnGenericDataDispatcher extends DataDispatcher implements RequestDataDispatcherSpyInterface
{
    public $spy;

    public function __construct(LoggerInterface $logger, DataDispatcherSpyInterface $spy)
    {
        parent::__construct($logger);
        $this->spy = $spy;
    }

    public static function getKeyword(): string
    {
        return 'generic';
    }

    public function send(array $data): bool
    {
        $this->spy->send($data);
        return true;
    }
}
