<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\Model\Form\DiscreteMultiValueField;
use FormRelay\Core\Model\Form\MultiValueField;

/**
 * @covers GeneralValueMapper
 */
class GeneralValueMapperTest extends AbstractValueMapperTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerValueMapper(GeneralValueMapper::class);
    }

    /** @test */
    public function mapNull()
    {
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

    /** @test */
    public function mapNestedMultiValue()
    {
        $this->fieldValue = new MultiValueField([
            'key1' => 'value1',
            'key2' => new MultiValueField(),
            'key3' => new MultiValueField([
                'key3_1' => 'value3_1',
                'key3_2' => new DiscreteMultiValueField([
                    'key_3_2_1' => 'value3_2_1',
                    'key_3_2_2' => 'value3_2_2',
                ]),
            ]),
        ]);
        $config = [
            'value1' => 'value1b',
            'value3_1' => 'value3_1b',
            'value3_2_1' => 'value3_2_1b',
            'value3_2_2' => 'value3_2_2b',
        ];
        $result = $this->runResolverProcess($config);

        $expected = new MultiValueField([
            'key1' => 'value1b',
            'key2' => new MultiValueField(),
            'key3' => new MultiValueField([
                'key3_1' => 'value3_1b',
                'key3_2' => new DiscreteMultiValueField([
                    'key_3_2_1' => 'value3_2_1b',
                    'key_3_2_2' => 'value3_2_2b',
                ]),
            ]),
        ]);

        $this->assertMultiValueEquals($expected, $result);
    }
}
