<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\EmptyEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

class EmptyEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->addEvaluation(EmptyEvaluation::class);
    }

    /** @test */
    public function checkEmptyFieldEmpty()
    {
        $this->data['field1'] = '';
        $config = [
            'field1' => [
                'empty' => true,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function checkEmptyFieldNotEmpty()
    {
        $this->data['field1'] = 'value';
        $config = [
            'field1' => [
                'empty' => true,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function checkNotEmptyFieldEmpty()
    {
        $this->data['field1'] = '';
        $config = [
            'field1' => [
                'empty' => false,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function checkNotEmptyFieldNotEmpty()
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => [
                'empty' => false,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function checkEmptyFieldDoesNotExist()
    {
        $config = [
            'field1' => [
                'empty' => true,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function checkNotEmptyFieldDoesNotExist()
    {
        $config = [
            'field1' => [
                'empty' => false,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    // TODO multiValue fields with no items will cause disjunctive evaluations to be always false
    //      and conjunctive evaluations to be always true
    //      we may need an additional check on the whole field (in eval()), not just on evalValue()
    /** @test */
    public function checkEmptyFieldMultiValueEmpty()
    {
        $this->markTestSkipped();
        $this->data['field1'] = new MultiValueField();
        $config = [
            'field1' => [
                'empty' => true,
            ]
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    // TODO multiValue fields with no items will cause disjunctive evaluations to be always false
    //      and conjunctive evaluations to be always true
    //      we may need an additional check on the whole field (in eval()), not just on evalValue()
    /** @test */
    public function checkNotEmptyFieldMultiValueEmpty()
    {
        $this->markTestSkipped();
        $this->data['field1'] = new MultiValueField();
        $config = [
            'field1' => [
                'empty' => false,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function checkEmptyFieldMultiValueNotEmpty()
    {
        $this->data['field1'] = new MultiValueField(['value1']);
        $config = [
            'field1' => [
                'empty' => true,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function checkNotEmptyFieldMultiValueNotEmpty()
    {
        $this->data['field1'] = new MultiValueField(['value1']);
        $config = [
            'field1' => [
                'empty' => false,
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }
}
