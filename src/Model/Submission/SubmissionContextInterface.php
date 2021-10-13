<?php

namespace FormRelay\Core\Model\Submission;

use ArrayAccess;

interface SubmissionContextInterface extends ArrayAccess
{
    public function toArray(): array;

    public function setInNamespace(string $namespace, string $name, string $value);
    public function addToNamespace(string $namespace, array $data);
    public function setNamespaceData(string $namespace, array $data);
    public function getFromNamespace(string $namespace, string $name, $default = null);
    public function getNamespaceData(string $namespace): array;
    public function removeFromNamespace(string $namespace, string $name);
    public function clearNamespace(string $namespace);

    public function setCookie(string $name, string $value);
    public function addCookies(array $cookies);
    public function setCookies(array $cookies);
    public function getCookie(string $name, $default = null);
    public function getCookies(): array;
    public function removeCookie(string $name);
    public function clearCookies();

    public function setRequestVariable(string $name, string $value);
    public function addRequestVariables(array $requestVariables);
    public function setRequestVariables(array $requestVariables);
    public function getRequestVariable(string $name, $default = null);
    public function getRequestVariables(): array;
    public function removeRequestVariable(string $name);
    public function clearRequestVariables();
}
