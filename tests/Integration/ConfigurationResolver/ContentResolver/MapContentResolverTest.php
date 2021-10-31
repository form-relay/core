<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\MapContentResolver;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

// NOTE more elaborate tests on value mappers can be found under the namespace
//      FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper

class MapContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(MapContentResolver::class);
        $this->registerBasicValueMappers();
    }

    /** @test */
    public function map()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'map' => [
                'value1' => 'value1b',
                'value2' => 'value2b',
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function noMap()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'map' => [
                'value2' => 'value2b',
                'value3' => 'value3b',
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function mapMultiValue()
    {
        $config = [
            'multiValue' => ['value1', 'value2', 'value3'],
            'map' => [
                'value1' => 'value1b',
                'value2' => 'value2b',
                'value3' => 'value3b',
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['value1b', 'value2b', 'value3b'], $result);
    }

    /** @test */
    public function noMapMultiValue()
    {
        $config = [
            'multiValue' => ['value1', 'value2', 'value3'],
            'map' => [
                'value4' => 'value4b',
                'value5' => 'value5b',
                'value6' => 'value6b',
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['value1', 'value2', 'value3'], $result);
    }

    /** @test */
    public function someMapMultiValue()
    {
        $config = [
            'multiValue' => ['value1', 'value2', 'value3'],
            'map' => [
                'value1' => 'value1b',
                'value3' => 'value3b',
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['value1b', 'value2', 'value3b'], $result);
    }
}
