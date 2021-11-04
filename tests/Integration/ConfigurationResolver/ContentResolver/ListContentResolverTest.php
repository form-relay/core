<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers ListContentResolver
 */
class ListContentResolverTest extends MultiValueContentResolverTest
{
    const RESOLVER_CLASS = ListContentResolver::class;
    const MULTI_VALUE_CLASS = MultiValueField::class;
    const KEYWORD = 'list';
}
