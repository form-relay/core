<?php

namespace FormRelay\Core\Model\Submission;

use ArrayObject;

class SubmissionContext extends ArrayObject implements SubmissionContextInterface
{
    const KEY_COOKIES = 'cookies';

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function setCookie(string $name, string $value)
    {
        $this[static::KEY_COOKIES][$name] = $value;
    }

    public function addCookies(array $cookies)
    {
        foreach ($cookies as $name => $value) {
            $this->setCookie($name, $value);
        }
    }

    public function setCookies(array $cookies)
    {
        $this[static::KEY_COOKIES] = $cookies;
    }

    public function getCookie(string $name, $default = null)
    {
        return $this[static::KEY_COOKIES][$name] ?? $default;
    }

    public function getCookies(): array
    {
        return $this[static::KEY_COOKIES] ?? [];
    }

    public function removeCookie(string $name)
    {
        if (isset($this[static::KEY_COOKIES][$name])) {
            unset($this[static::KEY_COOKIES][$name]);
        }
    }

    public function clearCookies()
    {
        $this[static::KEY_COOKIES] = [];
    }
}
