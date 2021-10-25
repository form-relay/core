<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;

class MultiValueContentResolverTest extends AbstractContentResolverTest
{
    const RESOLVER_CLASS = MultiValueContentResolver::class;
    const MULTI_VALUE_CLASS = MultiValueField::class;
    const KEYWORD = 'multiValue';

    protected function setUp(): void
    {
        parent::setUp();
        $this->addContentResolver(static::RESOLVER_CLASS);
    }

    /** @test */
    public function multiValueField()
    {
        $config = [
            static::KEYWORD => [3,5,17],
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(static::MULTI_VALUE_CLASS, $result);
        $this->assertEquals([3,5,17], $result->toArray());
    }

    /** @test */
    public function multiValueFieldEmpty()
    {
        $config = [
            static::KEYWORD => [],
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(static::MULTI_VALUE_CLASS, $result);
        $this->assertEquals([], $result->toArray());
    }

    /** @test */
    public function multiValueFieldContainsNull()
    {
        $config = [
            static::KEYWORD => [3,null,17],
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(static::MULTI_VALUE_CLASS, $result);
        $this->assertEquals([0 => 3, 2 => 17], $result->toArray());
    }

    /** @test */
    public function multiValueFieldContainsOnlyNulls()
    {
        $config = [
            static::KEYWORD => [null, null, null],
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(static::MULTI_VALUE_CLASS, $result);
        $this->assertEquals([], $result->toArray());
    }
}
