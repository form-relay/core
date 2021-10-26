<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\InsertDataContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\JoinContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\LoopDataContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\InEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\KeyEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class LoopDataContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addBasicEvaluations();
        $this->addContentResolver(LoopDataContentResolver::class);
        $this->addContentResolver(InsertDataContentResolver::class);
        $this->addContentResolver(FieldContentResolver::class);
    }

    /** @test */
    public function loopData()
    {
        $this->setupDummyData();
        $config = [
            'loopData' => true,
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals("field1 = value1\nfield2 = value2\nfield3 = value3\n", $result);
    }

    /** @test */
    public function loopDataInsertDataTemplate()
    {
        $this->setupDummyData();
        $config = [
            'loopData' => [
                'template' => [
                    SubmissionConfigurationInterface::KEY_SELF => '{key}:{value};',
                    'insertData' => true,
                ],
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals("field1:value1;field2:value2;field3:value3;", $result);
    }

    /** @test */
    public function loopDataWithGlue()
    {
        $this->setupDummyData();
        $config = [
            'loopData' => [
                'template' => ['field' => 'value'],
                'glue' => ','
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1,value2,value3', $result);
    }

    /** @test */
    public function loopDataWithCustomVars()
    {
        $this->setupDummyData();
        $config = [
            'loopData' => [
                'glue' => ',',
                'asKey' => 'customKey',
                'as' => 'customValue',
                'template' => [
                    SubmissionConfigurationInterface::KEY_SELF => '{customKey}={customValue}',
                    'insertData' => true,
                ],
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('field1=value1,field2=value2,field3=value3', $result);
    }

    /** @test */
    public function loopDataWithValueCondition()
    {
        $this->addEvaluation(InEvaluation::class);
        $this->setupDummyData();
        $config = [
            'loopData' => [
                'glue' => ',',
                'condition' => [
                    'in' => 'value1,value3',
                ],
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1,value3', $result);
    }

    /** @test */
    public function loopDataWithKeyCondition()
    {
        $this->addEvaluation(KeyEvaluation::class);
        $this->addEvaluation(InEvaluation::class);
        $this->setupDummyData();
        $config = [
            'loopData' => [
                'glue' => ',',
                'condition' => [
                    'key' => [
                        'in' => 'field1,field3',
                    ],
                ],
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1,value3', $result);
    }

    /** @test */
    public function loopDataWithOtherCondition()
    {
        $this->addEvaluation(KeyEvaluation::class);
        $this->addEvaluation(InEvaluation::class);
        $this->setupDummyData();
        $config = [
            'loopData' => [
                'glue' => ',',
                'condition' => [
                    'field3' => 'value3',
                ],
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1,value2,value3', $result);
    }

    // TODO: the glue option of the general content resolver should not be used for the multi value when cast to a string
    /** @test */
    public function loopDataFieldTemplateMultiValuesWithLoopGlue()
    {
        $this->markTestSkipped();
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $this->data['field2'] = 's';
        $this->data['field3'] = new MultiValueField(['c', 7, 'k']);
        $config = [
            'loopData' => [
                'glue' => ';',
                'template' => ['field' => 'value']
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('5,7,17;s;c,7,k', $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValue()
    {
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'loopData' => [
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals([5, 7, 17], $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValueJoined()
    {
        $this->addContentResolver(JoinContentResolver::class);
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'loopData' => [
                'template' => [
                    'field' => 'value',
                    'join' => true,
                ],
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals("5\n7\n17", $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValueJoinedWithGlue()
    {
        $this->addContentResolver(JoinContentResolver::class);
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'loopData' => [
                'template' => [
                    'field' => 'value',
                    'join' => [
                        'glue' => '-',
                    ],
                ],
            ],
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('5-7-17', $result);
    }
}
