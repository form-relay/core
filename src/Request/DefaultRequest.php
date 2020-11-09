<?php

namespace FormRelay\Core\Request;

class DefaultRequest implements RequestInterface
{
    public function getCookies(): array
    {
        return $_COOKIE;
    }
}
