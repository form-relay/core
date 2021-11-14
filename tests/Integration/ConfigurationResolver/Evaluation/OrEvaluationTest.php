<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\UpperCaseContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\OrEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\RegexpEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers OrEvaluation
 */
class OrEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(OrEvaluation::class);
        $this->setupDummyData();
    }

    /** @test */
    public function allTrue()
    {
        $config = [
            'or' => [
                'field1' => 'value1',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allFalse()
    {
        $config = [
            'or' => [
                'field1' => 'value4',
                'field2' => 'value5',
                'field3' => 'value6',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function someTrue()
    {
        $config = [
            'or' => [
                'field1' => 'value4',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function complexNestedConditionEvalTrue()
    {
        $this->registry->registerEvaluation(AndEvaluation::class);
        $config = [
            'or' => [
                1 => [
                    'and' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                ],
                2 => [
                    'and' => [
                        'field2' => 'value4',
                        'field3' => 'value5',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function complexNestedConditionEvalFalse()
    {
        $this->registry->registerEvaluation(AndEvaluation::class);
        $config = [
            'or' => [
                1 => [
                    'and' => [
                        'field1' => 'value1',
                        'field2' => 'value4',
                    ],
                ],
                2 => [
                    'and' => [
                        'field2' => 'value2',
                        'field3' => 'value5',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldLastEvalTrue()
    {
        $config = [
            'or' => [
                'equals' => 'value1',
                'field' => 'field1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldEvalTrue()
    {
        $config = [
            'or' => [
                'field' => 'field1',
                'equals' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldEvalFalse()
    {
        $config = [
            'or' => [
                'field' => 'field1',
                'equals' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFieldNonExistentFieldEvalFalse()
    {
        $config = [
            'or' => [
                'field' => 'field4',
                'equals' => 'value4',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFieldWithComplexEvaluationEvalTrue()
    {
        $this->registry->registerEvaluation(RegexpEvaluation::class);
        $config = [
            'or' => [
                'field' => 'field1',
                'regexp' => 'value[1]',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldWithComplexEvaluationEvalFalse()
    {
        $this->registry->registerEvaluation(RegexpEvaluation::class);
        $config = [
            'or' => [
                'field' => 'field1',
                'regexp' => 'value[23]',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'field' => 'field1',
                'index' => 1,
                'equals' => 'value1.2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldIndexLastEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'equals' => 'value1.2',
                'field' => 'field1',
                'index' => 1,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldLastIndexEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'equals' => 'value1.2',
                'index' => 1,
                'field' => 'field1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldIndexEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'field' => 'field1',
                'index' => 1,
                'equals' => 'value3.3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonExistentFieldEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'field' => 'field4',
                'index' => 1,
                'equals' => 'value4.2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonExistentIndexEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'or' => [
                'field' => 'field1',
                'index' => 2,
                'equals' => 'value1.3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonMultiValueFieldEvalFalse()
    {
        $config = [
            'or' => [
                'field' => 'field1',
                'index' => 1,
                'equals' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyScalarEvalTrue()
    {
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'or' => [
                'modify' => 'upperCase,trim',
                'field1' => 'VALUE1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyScalarEvalFalse()
    {
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'or' => [
                'modify' => 'upperCase,trim',
                'field1' => 'VALUE2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordLastModifyScalarEvalTrue()
    {
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'or' => [
                'field1' => 'VALUE1',
                'modify' => 'upperCase,trim',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordLastModifyScalarEvalFalse()
    {
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'or' => [
                'field1' => 'VALUE2',
                'modify' => 'upperCase,trim',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyArrayEvalTrue()
    {
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'or' => [
                'modify' => [
                    'upperCase' => true,
                    'trim' => true,
                ],
                'field1' => 'VALUE1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyArrayEvalFalse()
    {
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'or' => [
                'modify' => [
                    'upperCase' => true,
                    'trim' => true,
                ],
                'field1' => 'VALUE2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordLastModifyArrayEvalTrue()
    {
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'or' => [
                'field1' => 'VALUE1',
                'modify' => [
                    'upperCase' => true,
                    'trim' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordLastModifyArrayEvalFalse()
    {
        $this->registry->registerContentResolver(UpperCaseContentResolver::class);
        $this->registry->registerContentResolver(TrimContentResolver::class);
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'or' => [
                'field1' => 'VALUE2',
                'modify' => [
                    'upperCase' => true,
                    'trim' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
