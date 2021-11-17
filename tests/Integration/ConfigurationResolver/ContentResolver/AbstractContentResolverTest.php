<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

abstract class AbstractContentResolverTest extends AbstractConfigurationResolverTest
{
    protected function getGeneralResolverClass(): string
    {
        return GeneralContentResolver::class;
    }
}
