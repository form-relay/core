<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\RegistryInterface;

abstract class DataProvider implements DataProviderInterface
{
    const KEY_ENABLED = 'enabled';
    const DEFAULT_ENABLED = false;

    const KEY_MUST_EXIST = 'mustExist';
    const DEFAULT_MUST_EXIST = false;

    const KEY_MUST_BE_EMPTY= 'mustBeEmpty';
    const DEFAULT_MUST_BE_EMPTY = true;

    /** @var RegistryInterface */
    protected $registry;

    /** @var RequestInterface */
    protected $request;

    /** @var LoggerInterface */
    protected $logger;

    protected $configuration;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->request = $registry->getRequest();
        $this->logger = $registry->getLogger(static::class);
    }

    public static function getKeyword(): string
    {
        $namespaceParts = explode('\\', static::class);
        $class = array_pop($namespaceParts);
        $matches = [];
        if (preg_match('/^(.*)DataProvider$/', $class, $matches)) {
            return lcfirst($matches[1]);
        }
        return '';
    }

    abstract protected function processContext(SubmissionInterface $submission);
    abstract protected function process(SubmissionInterface $submission);

    protected function proceed(SubmissionInterface $submission): bool
    {
        /** @var GeneralEvaluation $evaluation */
        $context = new ConfigurationResolverContext($submission);
        $evaluation = $this->registry->getContentResolver(
            'general',
            $this->getConfig(static::KEY_ENABLED, static::DEFAULT_ENABLED),
            $context
        );
        $result = $evaluation->resolve();
        return !!$result;
    }

    protected function getConfig(string $key, $default = null)
    {
        if (array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        }
        return $default;
    }

    protected function appendToField(SubmissionInterface $submission, $key, $value, $glue = "\n"): bool
    {
        $data = $submission->getData();
        if (
            $this->getConfig(static::KEY_MUST_EXIST, static::DEFAULT_MUST_EXIST)
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
            $this->getConfig(static::KEY_MUST_EXIST, static::DEFAULT_MUST_EXIST)
            && !$data->fieldExists($key)
        ) {
            return false;
        }
        if (
            $this->getConfig(static::KEY_MUST_BE_EMPTY, static::DEFAULT_MUST_BE_EMPTY)
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
        $value = $submission->getContext()['cookies'][$cookieName] ?? null;
        if ($value !== null) {
            return $this->appendToField($submission, $field ?: $cookieName, $value, $glue);
        }
        return false;
    }

    protected function setFieldFromCookie(SubmissionInterface $submission, $cookieName, $field = null): bool
    {
        $value = $submission->getContext()['cookies'][$cookieName] ?? null;
        if ($value !== null) {
            return $this->setField($submission, $field ?: $cookieName, $value);
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

    protected function addCookieToContext(SubmissionInterface $submission, string $cookieName, $default = null): bool
    {
        $cookieValue = $this->request->getCookies()[$cookieName] ?? $default;
        if ($cookieValue !== null) {
            $submission->getContext()['cookies'][$cookieName] = $cookieValue;
            return true;
        }
        return false;
    }

    protected function addToContext(SubmissionInterface $submission, $key, $value)
    {
        $submission->getContext()[$key] = $value;
    }

    public function addContext(SubmissionInterface $submission)
    {
        $this->configuration = $submission->getConfiguration()->getDataProviderConfiguration(static::getKeyword());
        if ($this->proceed($submission)) {
            $this->processContext($submission);
        }
    }

    public function getWeight(): int
    {
        return 10;
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
