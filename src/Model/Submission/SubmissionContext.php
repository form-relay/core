<?php

namespace FormRelay\Core\Model\Submission;

use ArrayObject;

class SubmissionContext extends ArrayObject implements SubmissionContextInterface
{
    const NAMESPACE_COOKIES = 'cookies';
    const NAMESPACE_REQUEST_VARIABLES = 'request_variables';

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    // namespace handling

    public function setInNamespace(string $namespace, string $name, string $value)
    {
        $this[$namespace][$name] = $value;
    }

    public function addToNamespace(string $namespace, array $data)
    {
        foreach ($data as $name => $value) {
            $this->setInNamespace($namespace, $name, $value);
        }
    }

    public function setNamespaceData(string $namespace, array $data)
    {
        $this[$namespace] = $data;
    }

    public function getFromNamespace(string $namespace, string $name, $default = null)
    {
        return $this[$namespace][$name] ?? $default;
    }

    public function getNamespaceData(string $namespace): array
    {
        return $this[$namespace] ?? [];
    }

    public function removeFromNamespace(string $namespace, string $name)
    {
        if (isset($this[$namespace][$name])) {
            unset($this[$namespace][$name]);
        }
    }

    public function clearNamespace(string $namespace)
    {
        $this[$namespace] = [];
    }

    // namespace "cookies"

    public function setCookie(string $name, string $value)
    {
        $this->setInNamespace(static::NAMESPACE_COOKIES, $name, $value);
    }

    public function addCookies(array $cookies)
    {
        $this->addToNamespace(static::NAMESPACE_COOKIES, $cookies);
    }

    public function setCookies(array $cookies)
    {
        $this->setNamespaceData(static::NAMESPACE_COOKIES, $cookies);
    }

    public function getCookie(string $name, $default = null)
    {
        return $this->getFromNamespace(static::NAMESPACE_COOKIES, $name, $default);
    }

    public function getCookies(): array
    {
        return $this->getNamespaceData(static::NAMESPACE_COOKIES);
    }

    public function removeCookie(string $name)
    {
        $this->removeFromNamespace(static::NAMESPACE_COOKIES, $name);
    }

    public function clearCookies()
    {
        $this->clearNamespace(static::NAMESPACE_COOKIES);
    }

    // namespace "request_variables"

    public function setRequestVariable(string $name, string $value)
    {
        $this->setInNamespace(static::NAMESPACE_REQUEST_VARIABLES, $name, $value);
    }

    public function addRequestVariables(array $requestVariables)
    {
        $this->addToNamespace(static::NAMESPACE_REQUEST_VARIABLES, $requestVariables);
    }

    public function setRequestVariables(array $requestVariables)
    {
        $this->setNamespaceData(static::NAMESPACE_REQUEST_VARIABLES, $requestVariables);
    }

    public function getRequestVariable(string $name, $default = null)
    {
        return $this->getFromNamespace(static::NAMESPACE_REQUEST_VARIABLES, $name, $default);
    }

    public function getRequestVariables(): array
    {
        return $this->getNamespaceData(static::NAMESPACE_REQUEST_VARIABLES);
    }

    public function removeRequestVariable(string $name)
    {
        $this->removeFromNamespace(static::NAMESPACE_REQUEST_VARIABLES, $name);
    }

    public function clearRequestVariables()
    {
        $this->clearNamespace(static::NAMESPACE_REQUEST_VARIABLES);
    }
}
