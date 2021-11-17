<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers MultiValueContentResolver
 */
class MultiValueContentResolverTest extends AbstractContentResolverTest
{
    const RESOLVER_CLASS = MultiValueContentResolver::class;
    const MULTI_VALUE_CLASS = MultiValueField::class;
    const KEYWORD = 'multiValue';

    /** @test */
    public function multiValueField()
    {
        $config = [
            static::KEYWORD => [3,5,17],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([3,5,17], $result, static::MULTI_VALUE_CLASS);
    }

    /** @test */
    public function multiValueFieldEmpty()
    {
        $config = [
            static::KEYWORD => [],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([], $result, static::MULTI_VALUE_CLASS);
    }

    /** @test */
    public function multiValueFieldContainsNull()
    {
        $config = [
            static::KEYWORD => [3,null,17],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([0 => 3, 2 => 17], $result, static::MULTI_VALUE_CLASS);
    }

    /** @test */
    public function multiValueFieldContainsOnlyNulls()
    {
        $config = [
            static::KEYWORD => [null, null, null],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([], $result, static::MULTI_VALUE_CLASS);
    }
}
