<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\DiscreteMultiValueContentResolver;
use FormRelay\Core\Model\Form\DiscreteMultiValueField;

/**
 * @covers DiscreteMultiValueContentResolver
 */
class DiscreteMultiValueContentResolverTest extends MultiValueContentResolverTest
{
    const RESOLVER_CLASS = DiscreteMultiValueContentResolver::class;
    const MULTI_VALUE_CLASS = DiscreteMultiValueField::class;
    const KEYWORD = 'discreteMultiValue';
}
