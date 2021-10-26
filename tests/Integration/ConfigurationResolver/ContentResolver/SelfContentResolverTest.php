<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class SelfContentResolverTest extends AbstractContentResolverTest
{
    const KEY_SELF = SubmissionConfigurationInterface::KEY_SELF;

    /** @test */
    public function nullReturnsNull()
    {
        $config = null;
        $result = $this->runResolverTest($config);
        $this->assertNull($result);
    }

    /** @test */
    public function selfNullReturnsNull()
    {
        $config = [static::KEY_SELF => null];
        $result = $this->runResolverTest($config);
        $this->assertNull($result);
    }

    /** @test */
    public function stringReturnsItself()
    {
        $config = 'value1';
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function selfStringReturnsItself()
    {
        $config = [static::KEY_SELF => 'value1'];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function emptyStringReturnsEmptyString()
    {
        $config = '';
        $result = $this->runResolverTest($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function selfEmptyStringReturnsEmptyString()
    {
        $config = [static::KEY_SELF => ''];
        $result = $this->runResolverTest($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function complexFieldReturnsItself()
    {
        $config = new MultiValueField(['value1', 'value2']);
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['value1','value2'], $result);
    }

    /** @test */
    public function selfComplexFieldReturnsItself()
    {
        $config = [static::KEY_SELF => new MultiValueField(['value1', 'value2'])];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['value1', 'value2'], $result);
    }
}
