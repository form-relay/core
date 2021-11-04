<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\SelfContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

/**
 * @covers SelfContentResolver
 */
class SelfContentResolverTest extends AbstractContentResolverTest
{
    const KEY_SELF = SubmissionConfigurationInterface::KEY_SELF;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(SelfContentResolver::class);
    }

    /** @test */
    public function nullReturnsNull()
    {
        $config = null;
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function selfNullReturnsNull()
    {
        $config = [static::KEY_SELF => null];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function stringReturnsItself()
    {
        $config = 'value1';
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function selfStringReturnsItself()
    {
        $config = [static::KEY_SELF => 'value1'];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function emptyStringReturnsEmptyString()
    {
        $config = '';
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function selfEmptyStringReturnsEmptyString()
    {
        $config = [static::KEY_SELF => ''];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function complexFieldReturnsItself()
    {
        $config = new MultiValueField(['value1', 'value2']);
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1','value2'], $result);
    }

    /** @test */
    public function selfComplexFieldReturnsItself()
    {
        $config = [static::KEY_SELF => new MultiValueField(['value1', 'value2'])];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2'], $result);
    }
}
