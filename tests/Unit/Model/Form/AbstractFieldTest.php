<?php

namespace FormRelay\Core\Tests\Unit\Model\Form;

use FormRelay\Core\Model\Form\FieldInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractFieldTest extends TestCase
{
    const FIELD_CLASS = FieldInterface::class;

    /** @var FieldInterface */
    protected $subject;

    public static function assertFieldEquals($expected, $result)
    {
        static::assertInstanceOf(FieldInterface::class, $expected);
        static::assertInstanceOf(FieldInterface::class, $result);
        static::assertEquals($expected->pack(), $result->pack());
    }

    protected function createField(...$arguments)
    {
        $class = static::FIELD_CLASS;
        return new $class(...$arguments);
    }

    /** @test */
    abstract public function init();

    abstract public function castToStringProvider(): array;

    /**
     * @param $arguments
     * @param $stringRepresentation
     * @dataProvider castToStringProvider
     * @test
     */
    public function castToString($arguments, $stringRepresentation)
    {
        $this->subject = $this->createField(...$arguments);
        $result = (string)$this->subject;
        $this->assertEquals($stringRepresentation, $result);
    }

    abstract public function packProvider(): array;

    /**
     * @param $arguments
     * @param $packed
     * @dataProvider packProvider
     * @test
     */
    public function pack($arguments, $packed)
    {
        $this->subject = $this->createField(...$arguments);
        $result = $this->subject->pack();
        $this->assertEquals($packed, $result);
    }

    /** @test */
    public function packUnpack()
    {
        $this->subject = $this->createField();
        $packed = $this->subject->pack();
        $class = static::FIELD_CLASS;
        $unpacked = $class::unpack($packed);
        $this->assertFieldEquals($this->subject, $unpacked);
    }
}
