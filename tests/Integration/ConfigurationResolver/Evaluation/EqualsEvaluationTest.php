<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\EqualsEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers EqualsEvaluation
 */
class EqualsEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(EqualsEvaluation::class);
    }

    public function implicitEqualsProvider(): array
    {
        return [[true], [false]];
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function equalsScalarEvalTrue(bool $implicitEquals)
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'equals' => 'value1',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function equalsScalarEvalFalse(bool $implicitEquals)
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'equals' => 'value4',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldEqualsScalarEvalTrue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => 'field1',
            'equals' => 'value1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldEqualsScalarEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => 'field1',
            'equals' => 'value4',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function equalsComplexValueEvalTrue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'equals' => [
                    1 => 'val',
                    2 => 'ue1',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function equalsComplexValueEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'equals' => [
                    1 => 'val',
                    2 => 'ue4',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function multiValueEqualsScalarValueEvalTrue(bool $implicitEquals)
    {
        $this->submissionData['field1'] = new MultiValueField(['value1']);
        $config = [
            'field1' => [
                'equals' => 'value1',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function multiValueEqualsScalarValueEvalFalse(bool $implicitEquals)
    {
        $this->submissionData['field1'] = new MultiValueField(['value1']);
        $config = [
            'field1' => [
                'equals' => 'value2',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function multiValueEqualsCommaSeparatedListEvalTrue(bool $implicitEquals)
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => 'value1,value2',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function multiValueEqualsCommaSeparatedUnorderedListEvalTrue(bool $implicitEquals)
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => 'value2,value1',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function multiValueEqualsCommaSeparatedListEvalFalse(bool $implicitEquals)
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => 'value1,value3',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function multiValueEqualsCommaSeparatedListWithAdditionalValuesEvalFalse(bool $implicitEquals)
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => 'value1,value2,value3',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $implicitEquals
     * @dataProvider implicitEqualsProvider
     * @test
     */
    public function multiValueEqualsCommaSeparatedListWithTooFewValuesEvalFalse(bool $implicitEquals)
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => 'value1',
            ],
        ];
        if ($implicitEquals) {
            $config['field1'] = $config['field1']['equals'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function multiValueEqualsListValueEvalTrue()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value1', 'value2'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function multiValueEqualsUnorderedListValueEvalTrue()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value2', 'value1'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function multiValueEqualsListValueEvalFalse()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value1', 'value3'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function multiValueEqualsListWithAdditionalValuesEvalFalse()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value1', 'value2', 'value3'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function multiValueEqualsListWithTooFewValuesEvalFalse()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value1'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function scalarValueEqualsListEvalTrue()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value1'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function scalarValueEqualsListEvalFalse()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value2'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function scalarValueEqualsListWithTwoItemsEvalTrue()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = 'value1,value2';
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value1', 'value2'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function scalarValueEqualsListWithTwoItemsEvalFalse()
    {
        $this->registry->registerContentResolver(ListContentResolver::class);
        $this->submissionData['field1'] = 'value1,value2';
        $config = [
            'field1' => [
                'equals' => [
                    'list' => ['value1', 'value3'],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
