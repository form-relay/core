<?php

namespace FormRelay\Core\Service;

interface RegisterableInterface
{
    public static function getKeyword(): string;
    public function getWeight(): int;
}
