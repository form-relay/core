<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;

class GeneralContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function singleValue()
    {
        $config = 'value1';
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function concatenated()
    {
        $config = [
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1value2', $result);
    }

    /** @test */
    public function concatenatedWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => 'value2',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1,value2', $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptyValues()
    {
        $config = [
            'glue' => ',',
            1 => '',
            2 => '',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptyFirstValue()
    {
        $config = [
            'glue' => ',',
            1 => '',
            2 => 'value2',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function concatenateWithGlueAndEmptySecondValue()
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => '',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function singleMultiValueWithGlue()
    {
        $this->contentResolverClasses['multiValue'] = MultiValueContentResolver::class;
        $config = [
            'glue' => ',',
            1 => [
                'multiValue' => ['value1', 'value2'],
            ],
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals(['value1', 'value2'], $result->toArray());
    }

    /** @test */
    public function emptyScalarValueAndNonEmptyMultiValueWithGlue()
    {
        $this->contentResolverClasses['multiValue'] = MultiValueContentResolver::class;
        $config = [
            'glue' => ',',
            1 => '',
            2 => [
                'multiValue' => ['value1', 'value2'],
            ],
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals(['value1', 'value2'], $result->toArray());
    }

    // TODO resolver glue should not be used on multiValue fields
    //      we have the join resolver for that
    /** @test */
    public function multipleMultiValuesWithGlue()
    {
        $this->markTestSkipped();
        $this->contentResolverClasses['multiValue'] = MultiValueContentResolver::class;
        $config = [
            'glue' => ';',
            1 => [
                'multiValue' => ['value1', 'value2'],
            ],
            2 => [
                'multiValue' => ['value3', 'value4'],
            ],
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1,value2;value3,value4', $result);
    }

    /** @test */
    public function firstNullSecondNullWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => null,
            2 => null,
        ];
        $result = $this->runResolverTest($config);
        $this->assertNull($result);
    }

    /** @test */
    public function firstNullSecondNotNullWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => null,
            2 => 'value2',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value2', $result);
    }

    /** @test */
    public function firstNotNullSecondNullWithGlue()
    {
        $config = [
            'glue' => ',',
            1 => 'value1',
            2 => null,
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }
}
