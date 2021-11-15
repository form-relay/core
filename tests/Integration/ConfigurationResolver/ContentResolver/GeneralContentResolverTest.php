<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers GeneralContentResolver
 */
class GeneralContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(GeneralContentResolver::class);
    }

    /** @test */
    public function singleValue()
    {
        $config = 'value1';
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function concatenated()
    {
        $config = [
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1value2', $result);
    }

    /** @test */
    public function concatenatedWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2', $result);
    }

    /** @test */
    public function concatenatedWithGlueAtTheEnd()
    {
        $config = [
            1 => 'value1',
            2 => 'value2',
            'glue' => ',',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2', $result);
    }

    /** @test */
    public function concatenateWithGlueThatNeedsToBeResolvedUsingIfThen()
    {
        $this->registry->registerContentResolver(IfContentResolver::class);
        $this->registerBasicEvaluations();
        $this->submissionData['field1'] = 'value1';
        $config = [
            'glue' => [
                'if' => [
                    'field1' => 'value1',
                    'then' => '-',
                    'else' => '+',
                ],
            ],
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1-value2', $result);
    }

    /** @test */
    public function concatenateWithGlueThatNeedsToBeResolvedUsingIfElse()
    {
        $this->registry->registerContentResolver(IfContentResolver::class);
        $this->registerBasicEvaluations();
        $this->submissionData['field1'] = 'value1';
        $config = [
            'glue' => [
                'if' => [
                    'field1' => 'value2',
                    'then' => '-',
                    'else' => '+',
                ],
            ],
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1+value2', $result);
    }

    /** @test */
    public function concatenateWithGlueThatResolvesToNull()
    {
        $this->registry->registerContentResolver(IfContentResolver::class);
        $this->registerBasicEvaluations();
        $this->submissionData['field1'] = 'value1';
        $config = [
            'glue' => [
                'if' => [
                    'field1' => 'value1',
                    'else' => ';',
                ],
            ],
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1value2', $result);
    }

    /** @test */
    public function concatenateWithGlueThatNeedsToBeParsed()
    {
        $config = [
            'glue' => '\\s',
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("value1 value2", $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptyValues()
    {
        $config = [
            'glue' => ',',
            1 => '',
            2 => '',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptyFirstValue()
    {
        $config = [
            'glue' => ',',
            1 => '',
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptySecondValue()
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => '',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function singleMultiValueWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => [
                'multiValue' => ['value1', 'value2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2'], $result);
    }

    /** @test */
    public function emptyScalarValueAndNonEmptyMultiValueWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => '',
            2 => [
                'multiValue' => ['value1', 'value2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2'], $result);
    }

    /** @test */
    public function multipleMultiValuesWithGlue()
    {
        $config = [
            'glue' => ';',
            1 => [
                'multiValue' => ['value1', 'value2'],
            ],
            2 => [
                'multiValue' => ['value3', 'value4'],
            ],
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2;value3,value4', $result);
    }

    /** @test */
    public function firstNullSecondNullWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => null,
            2 => null,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function firstNullSecondNotNullWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => null,
            2 => 'value2',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function firstNotNullSecondNullWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => null,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }
}
