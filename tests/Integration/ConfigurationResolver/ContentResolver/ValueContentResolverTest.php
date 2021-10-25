<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ValueContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;

class ValueContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addContentResolver(ValueContentResolver::class);
    }

    /** @test */
    public function selfNullReturnsNull()
    {
        $config = ['value' => null];
        $result = $this->runResolverTest($config);
        $this->assertNull($result);
    }

    /** @test */
    public function selfStringReturnsItself()
    {
        $config = ['value' => 'value1'];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function selfEmptyStringReturnsEmptyString()
    {
        $config = ['value' => ''];
        $result = $this->runResolverTest($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function selfComplexFieldReturnsItself()
    {
        $config = ['value' => new MultiValueField(['value1', 'value2'])];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals(['value1', 'value2'], $result->toArray());
    }
}
