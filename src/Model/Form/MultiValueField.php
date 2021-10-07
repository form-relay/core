<?php

namespace FormRelay\Core\Model\Form;

use ArrayObject;

class MultiValueField extends ArrayObject implements FieldInterface
{
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function __toString($glue = ','): string
    {
        return implode($glue, $this->toArray());
    }

    public function pack(): array
    {
        return $this->toArray();
    }

    public static function unpack(array $packed): FieldInterface
    {
        return new static($packed);
    }
}
