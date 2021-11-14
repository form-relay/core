<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\Evaluation\FieldEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\IndexEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\NotEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers IndexEvaluation
 */
class IndexEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(FieldEvaluation::class);
        $this->registry->registerEvaluation(IndexEvaluation::class);
    }

    public function implicitFieldProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexEqualsEvalTrue(bool $fieldImplicit)
    {
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => 'value1.1',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithNonExistentIndexEqualsEvalFalse(bool $fieldImplicit)
    {
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index2' => 'value1.1',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexEqualsEvalFalse(bool $fieldImplicit)
    {
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => 'value2',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldDoesNotExistEqualsEvalFalse(bool $fieldImplicit)
    {
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field2' => [
                    'index' => [
                        'index1' => 'value2.1',
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexEqualsNotEvalFalse(bool $fieldImplicit)
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => [
                            'not' => 'value1.1',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithNonExistentIndexEqualsNotEvalTrue(bool $fieldImplicit)
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index2' => [
                            'not' => 'value1.1',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexEqualsNotEvalTrue(bool $fieldImplicit)
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => [
                            'not' => 'value2',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config = $config['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexNotEqualsEvalFalse(bool $fieldImplicit)
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'not' => [
                'field' => [
                    'field1' => [
                        'index' => [
                            'index1' => 'value1.1',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config['not'] = $config['not']['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithNonExistentIndexNotEqualsEvalTrue(bool $fieldImplicit)
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'not' => [
                'field' => [
                    'field1' => [
                        'index' => [
                            'index2' => 'value1.1',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config['not'] = $config['not']['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /**
     * @param bool $fieldImplicit
     * @dataProvider implicitFieldProvider
     * @test
     */
    public function fieldWithExistingIndexNotEqualsEvalTrue(bool $fieldImplicit)
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'not' => [
                'field' => [
                    'field1' => [
                        'index' => [
                            'index1' => 'value2',
                        ],
                    ],
                ],
            ],
        ];
        if ($fieldImplicit) {
            $config['not'] = $config['not']['field'];
        }
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldKeywordWithExistingIndexEqualsEvalTrue()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['not'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'not' => [
                    'index' => [
                        'index1' => 'value1.1',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldKeywordWithNonExistentIndexEqualsEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['not'] = new MultiValueField(['index1' => 'value1.1']);
        $config = [
            'field' => [
                'not' => [
                    'index' => [
                        'index2' => 'value1.1',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldKeywordEqualsEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['not'] = 'value1';
        $config = [
            'field' => [
                'not' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldKeywordDoesNotExistEqualsEvalFalse()
    {
        $this->registry->registerEvaluation(NotEvaluation::class);
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'not' => [
                    'index' => [
                        'index1' => 'value1',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function nestedMultiValueItemEqualsScalarValueEvalTrue()
    {
        $this->submissionData['field1'] = new MultiValueField([
            'index1' => new MultiValueField([
                'index1_1' => 'value1_1',
                'index1_2' => 'value1_2',
            ]),
            'index2' => new MultiValueField([
                'index2_1' => 'value2_1',
                'index2_2' => 'value2_2',
            ]),
        ]);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index2' => [
                            'index' => [
                                'index2_1' => 'value2_1',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function nestedMultiValueItemEqualsScalarValueEvalFalse()
    {
        $this->submissionData['field1'] = new MultiValueField([
            'index1' => new MultiValueField([
                'index1_1' => 'value1_1',
                'index1_2' => 'value1_2',
            ]),
            'index2' => new MultiValueField([
                'index2_1' => 'value2_1',
                'index2_2' => 'value2_2',
            ]),
        ]);
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index2' => [
                            'index' => [
                                'index2_1' => 'value2_2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function indexOnScalarValueEqualsScalarValueEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'index1' => 'value1',
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function emptyIndexClearsIndexEvalTrue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'someIndex' => [
                            'index' => [
                                '' => 'value1',
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function emptyIndexClearsIndexEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        'someIndex' => [
                            'index' => [
                                '' => 'value2',
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function emptyIndexDoesNothingIfNoIndexWasSetBeforeEvalTrue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        '' => 'value1',
                    ]
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function emptyIndexDoesNothingIfNoIndexWasSetBeforeEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field1' => [
                    'index' => [
                        '' => 'value2',
                    ]
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function newFieldClearsIndexEvalTrue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => [
                    'index' => [
                        'index2' => [
                            'field' => [
                                'field1' => 'value1',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function newFieldClearsIndexEvalFalse()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => [
                'field2' => [
                    'index' => [
                        'index2' => [
                            'field' => [
                                'field1' => 'value2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
