<?php

namespace FormRelay\Core\Tests\Unit\Utility;

use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Utility\ConfigurationUtility;
use PHPUnit\Framework\TestCase;

class ConfigurationUtilityTest extends TestCase
{
    /** @test */
    public function empty()
    {
        $result = ConfigurationUtility::mergeConfiguration([], []);
        $this->assertEquals([], $result);
    }

    public function mergeDontResolveNullProvider(): array
    {
        return [
            // target, source, expected
            'All empty' => [[], [], []],

            'Target key does not exist' => [
                [],
                ['key1' => 'value1'],
                ['key1' => 'value1']
            ],

            'Target key does not exist and source value is null' => [
                [],
                ['key1' => null],
                ['key1' => null]
            ],

            'Target value is null and source value is array' => [
                ['key1' => null],
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => ['key1.1' => 'value1.1']]
            ],

            'Target value is scalar and source value is array' => [
                ['key1' => 'value1'],
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => [SubmissionConfigurationInterface::KEY_SELF => 'value1', 'key1.1' => 'value1.1']]
            ],

            'Target value is array and source value is null' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => null],
                ['key1' => null]
            ],

            'Target value is array and source value scalar' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => 'value1'],
                ['key1' => [SubmissionConfigurationInterface::KEY_SELF => 'value1', 'key1.1' => 'value1.1']]
            ],

            'Target value is scalar and source value is null' => [
                ['key1' => 'value1'],
                ['key1' => null],
                ['key1' => null]
            ],

            'Target value is null and source value is null' => [
                ['key1' => null],
                ['key1' => null],
                ['key1' => null]
            ],

            'Target value is scalar and source value is scalar' => [
                ['key1' => 'value1'],
                ['key1' => 'value1b'],
                ['key1' => 'value1b']
            ],

            'Target value is array and source value is array' => [
                ['key1' => ['key1.1' => 'value1.1', 'key1.2' => 'value1.2']],
                ['key1' => ['key1.1' => 'value1.1b', 'key1.3' => 'value1.3b']],
                ['key1' => ['key1.1' => 'value1.1b', 'key1.2' => 'value1.2', 'key1.3' => 'value1.3b']]
            ],

            'Nested arrays' => [
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1b']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1b']]]
            ],
        ];
    }

    /**
     * @param $target
     * @param $source
     * @param $expected
     * @dataProvider mergeDontResolveNullProvider
     * @test
     */
    public function mergeDontResolveNull($target, $source, $expected)
    {
        $result = ConfigurationUtility::mergeConfiguration($target, $source, false);
        $this->assertEquals($expected, $result);
    }

    public function mergeResolveNullProvider(): array
    {
        return [
            // target, source, expected
            'All empty' => [[], [], []],

            'Target key does not exist' => [
                [],
                ['key1' => 'value1'],
                ['key1' => 'value1']
            ],

            'Target key does not exist and source value is null' => [
                [],
                ['key1' => null],
                []
            ],

            'Target value is null and source value is array' => [
                ['key1' => null],
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => ['key1.1' => 'value1.1']]
            ],

            'Target value is scalar and source value is array' => [
                ['key1' => 'value1'],
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => [SubmissionConfigurationInterface::KEY_SELF => 'value1', 'key1.1' => 'value1.1']]
            ],

            'Target value is array and source value is null' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => null],
                []
            ],

            'Target value is array and source value scalar' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => 'value1'],
                ['key1' => [SubmissionConfigurationInterface::KEY_SELF => 'value1', 'key1.1' => 'value1.1']]
            ],

            'Target value is scalar and source value is null' => [
                ['key1' => 'value1'],
                ['key1' => null],
                []
            ],

            'Target value is null and source value is null' => [
                ['key1' => null],
                ['key1' => null],
                []
            ],

            'Target value is scalar and source value is scalar' => [
                ['key1' => 'value1'],
                ['key1' => 'value1b'],
                ['key1' => 'value1b']
            ],

            'Target value is array and source value is array' => [
                ['key1' => ['key1.1' => 'value1.1', 'key1.2' => 'value1.2']],
                ['key1' => ['key1.1' => 'value1.1b', 'key1.3' => 'value1.3b']],
                ['key1' => ['key1.1' => 'value1.1b', 'key1.2' => 'value1.2', 'key1.3' => 'value1.3b']]
            ],

            'Nested arrays' => [
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1b']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1b']]]
            ],
        ];
    }

    /**
     * @param $target
     * @param $source
     * @param $expected
     * @dataProvider mergeResolveNullProvider
     * @test
     */
    public function mergeResolveNull($target, $source, $expected)
    {
        $result = ConfigurationUtility::mergeConfiguration($target, $source, true);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $target
     * @param $source
     * @param $expected
     * @dataProvider mergeDontResolveNullProvider
     * @dataProvider mergeResolveNullProvider
     * @test
     */
    public function resolvedNullDirectlyEqualsUnresolvedNullThenResolvedNull($target, $source, $expected)
    {
        $unresolved = ConfigurationUtility::mergeConfiguration($target, $source, false);
        $expected = ConfigurationUtility::resolveNullInMergedConfiguration($unresolved);

        $result = ConfigurationUtility::mergeConfiguration($target, $source, true);

        $this->assertEquals($expected, $result);
    }

    public function resolveNullProvider(): array
    {
        return [
            // config, expected
            'Empty' => [[], []],

            'Scalar value' => [
                ['key1' => 'value1'],
                ['key1' => 'value1']
            ],

            'Null' => [
                ['key1' => null],
                []
            ],

            'Array' => [
                ['key1' => ['key1.1' => 'value1.1']],
                ['key1' => ['key1.1' => 'value1.1']]
            ],

            'Array with null' => [
                ['key1' => ['key1.1' => null]],
                ['key1' => []]
            ],

            'Nested array with null' => [
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1', 'key1.1.2' => null, 'key1.1.3' => 'value1.1.3']]],
                ['key1' => ['key1.1' => ['key1.1.1' => 'value1.1.1', 'key1.1.3' => 'value1.1.3']]]
            ],
        ];
    }

    /**
     * @param $config
     * @param $expected
     * @dataProvider resolveNullProvider
     * @test
     */
    public function resolveNull($config, $expected)
    {
        $result = ConfigurationUtility::resolveNullInMergedConfiguration($config);
        $this->assertEquals($expected, $result);
    }
}
