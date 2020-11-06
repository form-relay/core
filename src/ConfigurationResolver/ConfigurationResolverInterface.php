<?php

namespace FormRelay\Core\ConfigurationResolver;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\Service\RegisterableInterface;
use FormRelay\Core\Service\RegistryInterface;

interface ConfigurationResolverInterface extends RegisterableInterface
{
    public static function getResolverType(): string;
    public static function getKeyword(): string;
    public function __construct(RegistryInterface $registry, $config, ConfigurationResolverContextInterface $context);
}
