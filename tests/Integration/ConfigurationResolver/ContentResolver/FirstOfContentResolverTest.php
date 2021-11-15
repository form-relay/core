<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\FirstOfContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\IfContentResolver;

/**
 * @covers FirstOfContentResolver
 */
class FirstOfContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(FirstOfContentResolver::class);
        $this->registry->registerContentResolver(FieldContentResolver::class);
    }

    /** @test */
    public function multipleFieldExistAndAreNotEmptyReturnsFirstField()
    {
        $this->submissionData['field1'] = 'value1';
        $this->submissionData['field2'] = 'value2';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function firstFieldDoesNotExistSecondFieldDoesExistAndIsNotEmptyReturnsSecondField()
    {
        $this->submissionData['field2'] = 'value2';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function firstFieldDoesExistButIsEmptySecondFieldDoesExistAndIsNotEmptyReturnsSecondField()
    {
        $this->submissionData['field1'] = '';
        $this->submissionData['field2'] = 'value2';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function firstFieldDoesNotExistSecondFieldIsEmptyReturnsEmptyString()
    {
        $this->submissionData['field2'] = '';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function firstFieldIsEmptySecondFieldDoesNotExistReturnsEmptyString()
    {
        $this->submissionData['field1'] = '';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function allFieldsAreEmptyReturnsEmptyString()
    {
        $this->submissionData['field1'] = '';
        $this->submissionData['field2'] = '';
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function noFieldExistsReturnsNull()
    {
        $config = [
            'firstOf' => [
                ['field' => 'field1'],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function fieldsAreSortedReturnsFirstField()
    {
        $this->submissionData['field1'] = 'value1';
        $this->submissionData['field2'] = 'value2';
        $config = [
            'firstOf' => [
                2 => ['field' => 'field2'],
                1 => ['field' => 'field1'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function fieldConditionFailsElseDoesNotExistReturnsSecondField()
    {
        $this->registry->registerContentResolver(IfContentResolver::class);
        $this->registerBasicEvaluations();
        $this->submissionData['field1'] = 'value1';
        $this->submissionData['field2'] = 'value2';
        $config = [
            'firstOf' => [
                [
                    'if' => [
                        'field1' => 'value2',
                        'then' => 'thenValue',
                    ],
                ],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function fieldConditionFailsElseDoesExistReturnsElsePart()
    {
        $this->registry->registerContentResolver(IfContentResolver::class);
        $this->registerBasicEvaluations();
        $this->submissionData['field1'] = 'value1';
        $this->submissionData['field2'] = 'value2';
        $config = [
            'firstOf' => [
                [
                    'if' => [
                        'field1' => 'value2',
                        'else' => 'elseValue',
                    ],
                ],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('elseValue', $result);
    }

    /** @test */
    public function fieldConditionSucceedsThenDoesNotExistReturnsSecondField()
    {
        $this->registry->registerContentResolver(IfContentResolver::class);
        $this->registerBasicEvaluations();
        $this->submissionData['field1'] = 'value1';
        $this->submissionData['field2'] = 'value2';
        $config = [
            'firstOf' => [
                [
                    'if' => [
                        'field1' => 'value1',
                        'else' => 'elseValue',
                    ],
                ],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function fieldConditionSucceedsThenDoesExistReturnsSecondField()
    {
        $this->registry->registerContentResolver(IfContentResolver::class);
        $this->registerBasicEvaluations();
        $this->submissionData['field1'] = 'value1';
        $this->submissionData['field2'] = 'value2';
        $config = [
            'firstOf' => [
                [
                    'if' => [
                        'field1' => 'value1',
                        'then' => 'thenValue',
                    ],
                ],
                ['field' => 'field2'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('thenValue', $result);
    }
}
