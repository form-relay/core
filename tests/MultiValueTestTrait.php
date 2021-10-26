<?php

namespace FormRelay\Core\Tests;

use FormRelay\Core\Model\Form\MultiValueField;

trait MultiValueTestTrait // extends \PHPUnit\Framework\TestCase
{
    public static function assertMultiValue($actual, string $class = MultiValueField::class)
    {
        if ($class !== MultiValueField::class) {
            static::assertInstanceOf(MultiValueField::class, $actual);
        }
        static::assertInstanceOf($class, $actual);
    }

    public static function assertMultiValueEquals(array $expected, $actual, string $class = MultiValueField::class)
    {
        static::assertMultiValue($actual, $class);
        /** @var MultiValueField $actual */
        static::assertEquals($expected, $actual->toArray());
    }

    public static function assertMultiValueEmpty($actual, string $class = MultiValueField::class)
    {
        static::assertMultiValue($actual, $class);
        /** @var MultiValueField $actual */
        static::assertEmpty($actual->toArray());
    }
}
