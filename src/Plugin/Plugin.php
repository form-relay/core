<?php

namespace FormRelay\Core\Plugin;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Service\PluginRegistryInterface;

abstract class Plugin implements PluginInterface
{
    /** @var PluginRegistryInterface */
    protected $registry;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $keyword;

    public function __construct(string $keyword, PluginRegistryInterface $registry, LoggerInterface $logger)
    {
        $this->keyword = $keyword;
        $this->registry = $registry;
        $this->logger = $logger;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function getWeight(): int
    {
        return 10;
    }
}
