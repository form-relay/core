<?php

namespace FormRelay\Core\Tests\Unit\Model\Form;

use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Tests\MultiValueTestTrait;

class MultiValueFieldTest extends AbstractFieldTest
{
    use MultiValueTestTrait;

    const FIELD_CLASS = MultiValueField::class;

    protected function createField(...$arguments)
    {
        if (empty($arguments)) {
            $arguments = [[5, 7, 17]];
        }
        return parent::createField(...$arguments);
    }


    /** @test */
    public function init()
    {
        $this->subject = $this->createField([5, 7, 17]);
        $this->assertMultiValueEquals([5, 7, 17], $this->subject, static::FIELD_CLASS);
    }

    /** @test */
    public function initEmpty()
    {
        $this->subject = $this->createField([]);
        $this->assertMultiValueEmpty($this->subject, static::FIELD_CLASS);
    }

    public function castToStringProvider(): array
    {
        return [
            [[[]],         ''],
            [[[5, 7, 17]], '5,7,17'],
            [[['','']],    ','],
        ];
    }

    public function castToStringWithGlueProvider(): array
    {
        return [
            [';', [5, 7, 17], '5;7;17'],
            [';', [],         ''],
            [';', ['', ''],   ';'],
            ['',  [5, 7, 17], '5717'],
            ['',  [],         ''],
            ['',  ['', ''],   ''],
        ];
    }

    /**
     * @param $glue
     * @param $values
     * @param $stringRepresentation
     * @dataProvider castToStringWithGlueProvider
     * @test
     */
    public function castToStringWithGlue($glue, $values, $stringRepresentation)
    {
        $this->subject = $this->createField($values);
        $this->subject->setGlue($glue);
        $result = (string)$this->subject;
        $this->assertEquals($stringRepresentation, $result);
    }

    /** @test */
    public function castToStringNested()
    {
        $this->subject = $this->createField([
            'a',
            $this->createField(['x', 'y', 'z']),
            'c',
        ]);
        $result = (string)$this->subject;
        $this->assertEquals('a,x,y,z,c', $result);
    }

    public function packProvider(): array
    {
        return [
            [[[]], []],
            [[[5, 7 ,17]], [5, 7, 17]],
        ];
    }

    // TODO test packUnpackNested?
}
