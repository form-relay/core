<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\InEvaluation;

class InEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addEvaluation(InEvaluation::class);
    }

    /** @test */
    public function nullIn()
    {
        $config = [
            'field1' => [
                'in' => '4,5,6',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function nullInList()
    {
        $config = [
            'field1' => [
                'in' => [
                    4, 5, 6,
                    // TODO: a list resolver should be possible here, but currently it isn't
                    //'list' => [4, 5, 6,],
                ]
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function in()
    {
        $this->data['field1'] = 5;
        $config = [
            'field1' => [
                'in' => '4,5,6',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function inList()
    {
        $this->addContentResolver(ListContentResolver::class);
        $this->data['field1'] = 5;
        $config = [
            'field1' => [
                'in' => [
                    4, 5, 6,
                    // TODO: a list resolver should be possible here, but currently it isn't
                    //'list' => [4, 5, 6,],
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notIn()
    {
        $this->data['field1'] = 5;
        $config = [
            'field1' => [
                'in' => '4,6,7',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notInList()
    {
        $this->data['field1'] = 5;
        $config = [
            'field1' => [
                'in' => [
                    4, 6, 7,
                    // TODO: a list resolver should be possible here, but currently it isn't
                    //'list' => [4,6,7],
                ],
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }
}
