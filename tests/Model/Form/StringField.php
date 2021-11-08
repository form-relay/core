<?php

namespace FormRelay\Core\Tests\Model\Form;

use FormRelay\Core\Model\Form\FieldInterface;

/**
 * Class StringField
 *
 * This dummy class has to exist because a mock can't have static methods
 * and the static method "unpack" is called by the QueryDataFactory
 *
 * @package FormRelay\Core\Tests\Mock\Model\Form
 */
class StringField implements FieldInterface
{
    public $value;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function pack(): array
    {
        return [(string)$this->value];
    }

    public static function unpack(array $packed): FieldInterface
    {
        $field = new StringField();
        $field->value = $packed[0];
        return $field;
    }
}
