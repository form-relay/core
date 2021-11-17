<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\LoopDataContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

/**
 * @covers LoopDataContentResolver
 */
class LoopDataContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function loopData()
    {
        $this->setupDummyData();
        $config = [
            'loopData' => true,
        ];
        $result = $this->runResolverProcess($config);
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
        $result = $this->runResolverProcess($config);
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
        $result = $this->runResolverProcess($config);
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
        $result = $this->runResolverProcess($config);
        $this->assertEquals('field1=value1,field2=value2,field3=value3', $result);
    }

    /** @test */
    public function loopDataWithValueCondition()
    {
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
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value3', $result);
    }

    /** @test */
    public function loopDataWithKeyCondition()
    {
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
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value3', $result);
    }

    /** @test */
    public function loopDataWithOtherCondition()
    {
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
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1,value2,value3', $result);
    }

    /** @test */
    public function loopDataFieldTemplateMultiValuesWithLoopGlue()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $this->submissionData['field2'] = 's';
        $this->submissionData['field3'] = new MultiValueField(['c', 7, 'k']);
        $config = [
            'loopData' => [
                'glue' => ';',
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('5,7,17;s;c,7,k', $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValue()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'loopData' => [
                'template' => ['field' => 'value'],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([5, 7, 17], $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValueJoined()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'loopData' => [
                'template' => [
                    'field' => 'value',
                    'join' => true,
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("5\n7\n17", $result);
    }

    /** @test */
    public function loopDataFieldTemplateOneMultiValueJoinedWithGlue()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
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
        $result = $this->runResolverProcess($config);
        $this->assertEquals('5-7-17', $result);
    }
}
