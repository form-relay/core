<?php

namespace FormRelay\Core\Model\Submission;

use ArrayAccess;

interface SubmissionContextInterface extends ArrayAccess
{
    public function toArray(): array;

    public function setCookie(string $name, string $value);
    public function addCookies(array $cookies);
    public function setCookies(array $cookies);
    public function getCookie(string $name, $default = null);
    public function getCookies(): array;
    public function removeCookie(string $name);
    public function clearCookies();
}
