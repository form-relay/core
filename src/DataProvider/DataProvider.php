<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\Helper\ConfigurationTrait;
use FormRelay\Core\Helper\RegisterableTrait;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\ClassRegistryInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class DataProvider implements DataProviderInterface
{
    use RegisterableTrait;
    use ConfigurationTrait;

    const KEY_ENABLED = 'enabled';
    const DEFAULT_ENABLED = false;

    const KEY_MUST_EXIST = 'mustExist';
    const DEFAULT_MUST_EXIST = false;

    const KEY_MUST_BE_EMPTY= 'mustBeEmpty';
    const DEFAULT_MUST_BE_EMPTY = true;

    /** @var ClassRegistryInterface */
    protected $registry;

    /** @var LoggerInterface */
    protected $logger;

    protected $configuration;

    public static function getClassType(): string
    {
        return 'DataProvider';
    }

    public function __construct(ClassRegistryInterface $registry, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->logger = $logger;
    }

    abstract protected function processContext(SubmissionInterface $submission, RequestInterface $request);
    abstract protected function process(SubmissionInterface $submission);

    protected function proceed(SubmissionInterface $submission): bool
    {
        /** @var GeneralEvaluation $evaluation */
        $context = new ConfigurationResolverContext($submission);
        $evaluation = $this->registry->getEvaluation(
            'general',
            $this->getConfig(static::KEY_ENABLED),
            $context
        );
        return $evaluation->eval();
    }

    protected function addRequestVariableToContext(SubmissionInterface $submission, RequestInterface $request, string $variableName): bool
    {
        $variableValue = $request->getRequestVariable($variableName);
        if (!GeneralUtility::isEmpty($variableValue)) {
            $submission->getContext()->setRequestVariable($variableName, $variableValue);
            return true;
        }
        return false;
    }

    protected function getRequestVariableFromContext(SubmissionInterface $submission, string $variableName)
    {
        return $submission->getContext()->getRequestVariable($variableName);
    }

    protected function addCookieToContext(SubmissionInterface $submission, RequestInterface $request, string $cookieName, $default = null): bool
    {
        $cookieValue = $request->getCookies()[$cookieName] ?? $default;
        if ($cookieValue !== null) {
            $submission->getContext()->setCookie($cookieName, $cookieValue);
            return true;
        }
        return false;
    }

    protected function getCookieFromContext(SubmissionInterface $submission, string $cookieName, $default = null)
    {
        return $submission->getContext()->getCookie($cookieName, $default);
    }

    protected function appendToField(SubmissionInterface $submission, $key, $value, $glue = "\n"): bool
    {
        $data = $submission->getData();
        if (
            $this->getConfig(static::KEY_MUST_EXIST)
            && !$data->fieldExists($key)
        ) {
            return false;
        }

        if ($data->fieldEmpty($key)) {
            $data[$key] = $value;
        } else {
            $data[$key] .= $glue . $value;
        }

        return true;
    }

    protected function setField(SubmissionInterface $submission, $key, $value): bool
    {
        $data = $submission->getData();
        if (
            $this->getConfig(static::KEY_MUST_EXIST)
            && !$data->fieldExists($key)
        ) {
            return false;
        }
        if (
            $this->getConfig(static::KEY_MUST_BE_EMPTY)
            && $data->fieldExists($key)
            && !$data->fieldEmpty($key)
        ) {
            return false;
        }
        $data[$key] = $value;
        return true;
    }

    protected function appendToFieldFromContext(SubmissionInterface $submission, $key, $field = null, $glue = "\n"): bool
    {
        $value = $submission->getContext()[$key] ?? null;
        if ($value !== null) {
            return $this->appendToField($submission, $field ?: $key, $value, $glue);
        }
        return false;
    }

    protected function setFieldFromContext(SubmissionInterface $submission, $key, $field = null): bool
    {
        $value = $submission->getContext()[$key] ?? null;
        if ($value !== null) {
            return $this->setField($submission, $field ?: $key, $value);
        }
        return false;
    }

    protected function appendToFieldFromCookie(SubmissionInterface $submission, $cookieName, $field = null, $glue = "\n"): bool
    {
        $value = $this->getCookieFromContext($submission, $cookieName);
        if ($value !== null) {
            return $this->appendToField($submission, $field ?: $cookieName, $value, $glue);
        }
        return false;
    }

    protected function setFieldFromCookie(SubmissionInterface $submission, $cookieName, $field = null): bool
    {
        $value = $this->getCookieFromContext($submission, $cookieName);
        if ($value !== null) {
            return $this->setField($submission, $field ?: $cookieName, $value);
        }
        return false;
    }

    protected function appendToFieldFromRequestVariable(SubmissionInterface $submission, $variableName, $field = null, $glue = "\n"): bool
    {
        $value = $this->getRequestVariableFromContext($submission, $variableName);
        if ($value !== null) {
            return $this->appendToField($submission, $field ?: $variableName, $value, $glue);
        }
        return false;
    }

    protected function setFieldFromRequestVariable(SubmissionInterface $submission, $variableName, $field = null): bool
    {
        $value = $this->getRequestVariableFromContext($submission, $variableName);
        if ($value !== null) {
            return $this->setField($submission, $field ?: $variableName, $value);
        }
        return false;
    }

    public function addData(SubmissionInterface $submission)
    {
        $this->configuration = $submission->getConfiguration()->getDataProviderConfiguration(static::getKeyword());
        if ($this->proceed($submission)) {
            $this->process($submission);
        }
    }

    protected function addToContext(SubmissionInterface $submission, $key, $value)
    {
        $submission->getContext()[$key] = $value;
    }

    public function addContext(SubmissionInterface $submission, RequestInterface $request)
    {
        $this->configuration = $submission->getConfiguration()->getDataProviderConfiguration(static::getKeyword());
        if ($this->proceed($submission)) {
            $this->processContext($submission, $request);
        }
    }

    public static function getDefaultConfiguration(): array
    {
        return [
            static::KEY_ENABLED => static::DEFAULT_ENABLED,
            static::KEY_MUST_EXIST => static::DEFAULT_MUST_EXIST,
            static::KEY_MUST_BE_EMPTY => static::DEFAULT_MUST_BE_EMPTY,
        ];
    }
}
