<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\DefaultContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class DefaultContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(DefaultContentResolver::class);
    }

    /** @test */
    public function defaultOnly()
    {
        $config = [
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    /** @test */
    public function null()
    {
        $config = [
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    /** @test */
    public function emptyString()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => '',
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    /** @test */
    public function emptyStringWhenTrimmed()
    {
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => ' ',
            'trim' => true,
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    /** @test */
    public function nonEmptyString()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function multiValue()
    {
        $config = [
            'multiValue' => ['value1', 'value2'],
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['value1','value2'], $result);
    }

    /** @test */
    public function emptyMultiValue()
    {
        $config = [
            'multiValue' => [],
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    // TODO GeneralUtility::isEmpty should count the items of multiValue fields
    //      instead of imploding the whole field
    /** @test */
    public function multiValueOneEmptyItem()
    {
        $this->markTestSkipped();
        $config = [
            'multiValue' => [''],
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals([''], $result);
    }

    /** @test */
    public function multiValueMultipleEmptyItems()
    {
        $config = [
            'multiValue' => ['', ''],
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['',''], $result);
    }

    /** @test */
    public function defaultIsMultiValue()
    {
        $config = [
            'default' => [
                'multiValue' => ['value1', 'value2'],
            ]
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['value1','value2'], $result);
    }
}
