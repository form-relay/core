<?php

namespace FormRelay\Core\Model\Form;

use ArrayObject;

class MultiValueField extends ArrayObject implements FieldInterface
{
    protected $glue = ',';

    public function __construct(array $a = []) {
        parent::__construct($a);
    }

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function setGlue(string $glue)
    {
        $this->glue = $glue;
    }

    public function getGlue(): string
    {
        return $this->glue;
    }

    public function __toString(): string
    {
        return implode($this->glue, $this->toArray());
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
