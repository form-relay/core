<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\LowerCaseContentResolver;

/**
 * @covers LowerCaseContentResolver
 */
class LowerCaseContentResolverTest extends AbstractModifierContentResolverTest
{
    const KEYWORD = 'lowerCase';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(LowerCaseContentResolver::class);
    }

    public function modifyProvider(): array
    {
        return [
            [null,     null],
            ['VALUE1', 'value1'],
            ['value1', 'value1'],
            ['1_2_3',  '1_2_3'],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [[], []],
            [['Value1', 'VALUE2', 'value3'], ['value1', 'value2', 'value3']],
        ];
    }
}
