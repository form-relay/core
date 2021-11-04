<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\Model\Form\DiscreteMultiValueField;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

class GeneralValueMapperTest extends AbstractValueMapperTest
{
    // TODO there is legacy code trying to fetch the field value from the context if null is passed
    //      which doesn't really make sense anymore and also messes up this test
    /** @test */
    public function mapNull()
    {
        $this->markTestSkipped();
        $this->fieldValue = null;
        $config = [
            'value0' => 'value0b',
            'value1' => 'value1b',
            'value2' => 'value2b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function mapMatches()
    {
        $this->fieldValue = 'value1';
        $config = [
            'value0' => 'value0b',
            'value1' => 'value1b',
            'value2' => 'value2b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function mapDoesNotMatch()
    {
        $this->fieldValue = 'value1';
        $config = [
            'value0' => 'value0b',
            'value2' => 'value2b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function mapMultiValueAllMatch()
    {
        $this->fieldValue = new MultiValueField(['value1', 'value2', 'value3']);
        $config = [
            'value1' => 'value1b',
            'value2' => 'value2b',
            'value3' => 'value3b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1b', 'value2b', 'value3b'], $result);
    }

    /** @test */
    public function mapMultiValueSomeMatch()
    {
        $this->fieldValue = new MultiValueField(['value1', 'value2', 'value3']);
        $config = [
            'value1' => 'value1b',
            'value3' => 'value3b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1b', 'value2', 'value3b'], $result);
    }

    /** @test */
    public function mapMultiValueNoneMatch()
    {
        $this->fieldValue = new MultiValueField(['value1', 'value2', 'value3']);
        $config = [
            'value4' => 'value4b',
            'value5' => 'value5b',
            'value6' => 'value6b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2', 'value3'], $result);
    }

    /** @test */
    public function mapDiscreteMultiValue()
    {
        $this->fieldValue = new DiscreteMultiValueField(['value1', 'value2', 'value3']);
        $config = [
            'value1' => 'value1b',
            'value3' => 'value3b',
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1b', 'value2', 'value3b'], $result, DiscreteMultiValueField::class);
    }
}
