<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

abstract class AbstractValueMapperTest extends AbstractConfigurationResolverTest
{
    protected $fieldValue = null;

    protected function setUp()
    {
        parent::setUp();
        $this->addBasicValueMappers();
    }

    protected function getGeneralResolverClass(): string
    {
        return GeneralValueMapper::class;
    }

    protected function processResolver(GeneralConfigurationResolverInterface $resolver)
    {
        /** @var GeneralValueMapper $resolver */
        return $resolver->resolve($this->fieldValue);
    }
}
