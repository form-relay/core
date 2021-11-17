<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers AndEvaluation
 */
class AndEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDummyData(3);
    }

    /** @test */
    public function allTrue()
    {
        $config = [
            'and' => [
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
            'and' => [
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
            'and' => [
                'field1' => 'value4',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function complexNestedConditionEvalTrue()
    {
        $config = [
            'and' => [
                1 => [
                    'or' => [
                        'field1' => 'value1',
                        'field2' => 'value4',
                    ],
                ],
                2 => [
                    'or' => [
                        'field2' => 'value5',
                        'field3' => 'value3',
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
        $config = [
            'and' => [
                1 => [
                    'or' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                ],
                2 => [
                    'or' => [
                        'field2' => 'value4',
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
            'equals' => 'value1',
            'field' => 'field1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldEvalTrue()
    {
        $config = [
            'field' => 'field1',
            'equals' => 'value1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldEvalFalse()
    {
        $config = [
            'field' => 'field1',
            'equals' => 'value2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFieldNonExistentFieldEvalFalse()
    {
        $config = [
            'field' => 'field4',
            'equals' => 'value4',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFieldWithComplexEvaluationEvalTrue()
    {
        $config = [
            'field' => 'field1',
            'and' => [
                ['regexp' => 'value[12]'],
                ['regexp' => 'value[0-9]'],
            ]
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFieldWithComplexEvaluationEvalFalse()
    {
        $config = [
            'field' => 'field1',
            'and' => [
                ['regexp' => 'value[23]'],
                ['regexp' => 'value[0-9]'],
            ]
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldWithSubEvaluationsEvalTrue()
    {
        $config = [
            'field2' => 'value2',
            'field' => 'field1',
            'equals' => 'value1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldWithSubEvaluationsEvalFalse()
    {
        $config = [
            'field2' => 'value2',
            'field' => 'field1',
            'equals' => 'value2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field1',
            'index' => 1,
            'equals' => 'value1.2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldIndexLastEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'equals' => 'value1.2',
            'field' => 'field1',
            'index' => 1,
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldLastIndexEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'equals' => 'value1.2',
            'index' => 1,
            'field' => 'field1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeywordFieldIndexEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field1',
            'index' => 1,
            'equals' => 'value3.3',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonExistentFieldEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field4',
            'index' => 1,
            'equals' => 'value4.2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonExistentIndexEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1.1', 'value1.2']);
        $config = [
            'field' => 'field1',
            'index' => 2,
            'equals' => 'value1.3',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeywordFieldIndexNonMultiValueFieldEvalFalse()
    {
        $config = [
            'field' => 'field1',
            'index' => 1,
            'equals' => 'value1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyScalarEvalTrue()
    {
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'modify' => 'upperCase,trim',
            'field1' => 'VALUE1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyScalarEvalFalse()
    {
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'modify' => 'upperCase,trim',
            'field1' => 'VALUE2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordLastModifyScalarEvalTrue()
    {
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'field1' => 'VALUE1',
            'modify' => 'upperCase,trim',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordLastModifyScalarEvalFalse()
    {
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'field1' => 'VALUE2',
            'modify' => 'upperCase,trim',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyArrayEvalTrue()
    {
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'modify' => [
                'upperCase' => true,
                'trim' => true,
            ],
            'field1' => 'VALUE1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordFirstModifyArrayEvalFalse()
    {
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'modify' => [
                'upperCase' => true,
                'trim' => true,
            ],
            'field1' => 'VALUE2',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function staticKeyWordLastModifyArrayEvalTrue()
    {
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'field1' => 'VALUE1',
            'modify' => [
                'upperCase' => true,
                'trim' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function staticKeyWordLastModifyArrayEvalFalse()
    {
        $this->submissionData['field1'] = ' value1 ';
        $config = [
            'field1' => 'VALUE2',
            'modify' => [
                'upperCase' => true,
                'trim' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
