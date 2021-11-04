<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

/**
 * @covers GeneralEvaluation
 */
class GeneralEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(GeneralEvaluation::class);
    }


    public function provider()
    {
        $scalar1 = 'value1';
        $scalar2 = 'value2';
        $array1 = ['key1' => 'value1'];
        $array2 = ['key2' => 'value2'];

        return [
            [null,     null,     false, /* => */ null],
            [null,     null,     true,  /* => */ null],
            [null,     $scalar2, false, /* => */ $scalar2],
            [null,     $scalar2, true,  /* => */ null],
            [null,     $array2,  false, /* => */ $array2],
            [null,     $array2,  true,  /* => */ null],

            ['',       null,     false, /* => */ null],
            ['',       null,     true,  /* => */ ''],
            ['',       '',       false, /* => */ ''],
            ['',       '',       true,  /* => */ ''],
            ['',       $scalar2, false, /* => */ $scalar2],
            ['',       $scalar2, true,  /* => */ ''],
            ['',       $array2,  false, /* => */ $array2],
            ['',       $array2,  true,  /* => */ ''],

            [$scalar1, null,     false, /* => */ null],
            [$scalar1, null,     true,  /* => */ $scalar1],
            [$scalar1, '',       false, /* => */ ''],
            [$scalar1, '',       true,  /* => */ $scalar1],
            [$scalar1, $scalar2, false, /* => */ $scalar2],
            [$scalar1, $scalar2, true,  /* => */ $scalar1],
            [$scalar1, $array2,  false, /* => */ $array2],
            [$scalar1, $array2,  true,  /* => */ $scalar1],

            [$array1, null,      false, /* => */ null],
            [$array1, null,      true,  /* => */ $array1],
            [$array1, '',        false, /* => */ ''],
            [$array1, '',        true,  /* => */ $array1],
            [$array1, $scalar2,  false, /* => */ $scalar2],
            [$array1, $scalar2,  true,  /* => */ $array1],
            [$array1, $array2,   false, /* => */ $array2],
            [$array1, $array2,   true,  /* => */ $array1],
        ];
    }

    protected function runThenElse($then, $else, $eval, $expected, $useNullOnThen, $useNullOnElse)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $eval,
        ];
        if ($then !== null || $useNullOnThen) {
            $config['then'] = $then;
        }
        if ($else !== null || $useNullOnElse) {
            $config['else'] = $else;
        }

        $result = $this->runResolverProcess($config);

        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @param $then
     * @param $else
     * @param $eval
     * @param $expected
     *
     * @dataProvider provider
     * @test
     */
    public function thenElse($then, $else, $eval, $expected)
    {
        $this->runThenElse($then, $else, $eval, $expected, false, false);
        if ($else === null) {
            $this->runThenElse($then, $else, $eval, $expected, false, true);
        }
        if ($then === null) {
            $this->runThenElse($then, $else, $eval, $expected, true, false);
        }
        if ($then === null && $else === null) {
            $this->runThenElse($then, $else, $eval, $expected, true, true);
        }
    }
}
