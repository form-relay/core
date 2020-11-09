<?php

namespace FormRelay\Core\ConfigurationResolver;

use FormRelay\Core\Service\RegisterableInterface;

interface ConfigurationResolverInterface extends RegisterableInterface
{
    public static function getResolverType(): string;
    public static function getKeyword(): string;
}
