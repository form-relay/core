<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\UpperCaseContentResolver;

/**
 * @covers UpperCaseContentResolver
 */
class UpperCaseContentResolverTest extends AbstractModifierContentResolverTest
{
    const KEYWORD = 'upperCase';

    public function modifyProvider(): array
    {
        return [
            [null,     null],
            ['value1', 'VALUE1'],
            ['VALUE1', 'VALUE1'],
            ['1_2_3',  '1_2_3'],
        ];
    }

    public function modifyMultiValueProvider(): array
    {
        return [
            [[], []],
            [['Value1', 'VALUE2', 'value3'], ['VALUE1', 'VALUE2', 'VALUE3']],
        ];
    }
}
