<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class SelfEvaluationTest extends AbstractEvaluationTest
{
    const KEY_SELF = SubmissionConfigurationInterface::KEY_SELF;

    /** @test */
    public function evalFalseReturnsFalse()
    {
        $config = false;
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }
    /** @test */
    public function evalSelfFalseReturnsFalse()
    {
        $config = [static::KEY_SELF => false];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalTrueReturnsTrue()
    {
        $config = true;
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfTrueReturnsTrue()
    {
        $config = [static::KEY_SELF => true];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalEmptyStringReturnsFalse()
    {
        $config = '';
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfEmptyStringReturnsFalse()
    {
        $config = [static::KEY_SELF => ''];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalNonEmptyStringReturnsTrue()
    {
        $config = 'value1';
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfNonEmptyStringReturnsTrue()
    {
        $config = [static::KEY_SELF => 'value1'];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalNullReturnsFalse()
    {
        $config = null;
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfNullReturnsFalse()
    {
        $config = [static::KEY_SELF => null];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalStringZeroReturnsFalse()
    {
        $config = '0';
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfStringZeroReturnsFalse()
    {
        $config = [static::KEY_SELF => '0'];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalStringOneReturnsTrue()
    {
        $config = '1';
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfStringOneReturnsTrue()
    {
        $config = [static::KEY_SELF => '1'];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalIntZeroReturnsFalse()
    {
        $config = 0;
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalSelfIntZeroReturnsFalse()
    {
        $config = [static::KEY_SELF => 0];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalIntOneReturnsTrue()
    {
        $config = 1;
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfIntOneReturnsTrue()
    {
        $config = [static::KEY_SELF => 1];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalIntPositiveReturnsTrue()
    {
        $config = 42;
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalSelfIntPositiveReturnsTrue()
    {
        $config = [static::KEY_SELF => 42];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalFieldEquals()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                static::KEY_SELF => 'value1',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function evalFieldEqualsNot()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field1' => [
                static::KEY_SELF => 'value2',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function evalFieldDoesNotExist()
    {
        $this->markTestSkipped();
        $config = [
            'field1' => [
                static::KEY_SELF => 'value1',
            ],
        ];
        $result = $this->runEvaluationTest($config);
        $this->assertFalse($result);
    }
}
