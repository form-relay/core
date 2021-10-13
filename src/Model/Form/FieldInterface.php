<?php

namespace FormRelay\Core\Model\Form;

interface FieldInterface
{
    public function __toString(): string;

    public function pack(): array;
    public static function unpack(array $packed): FieldInterface;
}
