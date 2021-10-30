<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\AllInEvaluation;
use FormRelay\Core\Model\Form\MultiValueField;

class AllInEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addEvaluation(AllInEvaluation::class);
    }

    /** @test */
    public function allInNull()
    {
        $config = [
            'field1' => [
                'allIn' => '4,5,6,7,8,16,17,18',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allIn()
    {
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => '4,5,6,7,8,16,17,18',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allInList()
    {
        $this->addContentResolver(ListContentResolver::class);
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => [
                    4, 5, 6, 7, 8, 16, 17, 18,
                    // TODO: a list resolver should be possible here, but currently it isn't
                    //'list' => [4, 5, 6, 7, 8, 16, 17, 18],
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notAllIn()
    {
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => '4,5,6,7,8,16,18',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notAllInList()
    {
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => [
                    4, 5, 6, 7, 8, 16, 18,
                    // TODO: a list resolver should be possible here, but currently it isn't
                    //'list' => [4,5,6,7,8,16,18],
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function noneIn()
    {
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => '4,6,8,16,18',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function noneInList()
    {
        $this->data['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            'field1' => [
                'allIn' => [
                    4, 6, 8, 16, 18,
                    // TODO: a list resolver should be possible here, but currently it isn't
                    //'list' => [4, 6, 8, 16, 18,],
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }
}
