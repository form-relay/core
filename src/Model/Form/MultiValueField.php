<?php

namespace FormRelay\Core\Model\Form;

use ArrayObject;

class MultiValueField extends ArrayObject implements FieldInterface
{
    public function __toString($glue = ','): string
    {
        return implode($glue, iterator_to_array($this));
    }
}
