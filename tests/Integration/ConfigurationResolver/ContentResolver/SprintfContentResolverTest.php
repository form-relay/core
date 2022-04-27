<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\SprintfContentResolver;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

/**
 * @covers SprintfContentResolver
 */
class SprintfContentResolverTest extends AbstractContentResolverTest
{
    // NOTE: the parent class AbstractModifierContentResolverTest can't be used here
    //       because this content resolver handles multi-value fields differently

    /** @test */
    public function nullIsNotProcessed()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => null,
            'sprintf' => 'format1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function emptyFormatLeadsToEmptyResult()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'sprintf' => '',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function boolFalseFormatDoesNotGetProcessed()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'sprintf' => false,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function staticFormatIsPassedThrough()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'sprintf' => 'format1',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('format1', $result);
    }

    /** @test */
    public function valueIsUsedAsPlaceholder()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'sprintf' => '%s',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function valueIsUsedAsPlaceholderInComplexFormat()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'sprintf' => 'format1:%s',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('format1:value1', $result);
    }

    /** @test */
    public function floatFormatting()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 1.2,
            'sprintf' => '%01.2f',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('1.20', $result);
    }

    /** @test */
    public function multiValueIsUsedForMultiplePlaceholders()
    {
        $config = [
            'multiValue' => ['value1', 'value2'],
            'sprintf' => '%s:%s',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1:value2', $result);
    }

    /** @test */
    public function multiValueUsedAsPlaceholdersWithMoreValuesThanPlaceholders()
    {
        $config = [
            'multiValue' => ['value1', 'value2'],
            'sprintf' => '%s',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function multiValueIsJoinedAndUsedAsOnePlaceholder()
    {
        $config = [
            'multiValue' => ['value1', 'value2'],
            'join' => ['glue' => ','],
            'sprintf' => '%s',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2', $result);
    }

    /** @test */
    public function multiValueUsedForFloatingpointPlaceholders()
    {
        $config = [
            'multiValue' => [1.2, 34.567],
            'sprintf' => '%01.2f - %01.2f',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('1.20 - 34.57', $result);
    }

    /** @test */
    public function nestedMultiValuesAreFlattenOutInPlaceholders()
    {
        $config = [
            'multiValue' => [
                ['multiValue' => ['a', 'b']],
                'c',
                ['multiValue' => ['d']],
                'e',
                ['multiValue' => []],
                'f'
            ],
            'sprintf' => '%s:%s:%s:%s:%s:%s',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('a,b:c:d:e::f', $result);
    }

    /** @test */
    public function concatenatedFormat()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'sprintf' => [
                'glue' => ':',
                'format1',
                '%s',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('format1:value1', $result);
    }

    /** @test */
    public function conditionalFormatWithTwoOptions()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'sprintf' => ['if' => [
                'field1' => 'value1',
                'then' => 'formatThen:%s',
                'else' => 'formatElse:%s',
            ]],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('formatThen:value1', $result);
    }

    /** @test */
    public function conditionalFormatWithNullOptionDoesNotGetProcessed()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value2',
            'sprintf' => ['if' => [
                'field1' => 'value3',
                'then' => 'formatThen:%s',
            ]],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }
}
