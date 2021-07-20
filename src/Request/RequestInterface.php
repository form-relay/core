<?php

namespace FormRelay\Core\Request;

interface RequestInterface
{
    public function getCookies(): array;
    public function getIpAddress(): string;
    public function getRequestVariable(string $name);
}
