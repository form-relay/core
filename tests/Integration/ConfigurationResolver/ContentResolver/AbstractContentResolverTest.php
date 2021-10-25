<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

abstract class AbstractContentResolverTest extends AbstractConfigurationResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addBasicContentResolvers();
    }

    protected function getGeneralResolverClass(): string
    {
        return GeneralContentResolver::class;
    }
}
