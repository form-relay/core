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

    protected $registry;
    protected $request;
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

    protected function setField(SubmissionInterface $submission, $key, $value): bool
    {
        if (
            $this->getConfig(static::KEY_MUST_EXIST, static::DEFAULT_MUST_EXIST)
            && !$submission->getData()->keyExists($key)
        ) {
            return false;
        }
        if (
            $this->getConfig(static::KEY_MUST_BE_EMPTY, static::DEFAULT_MUST_BE_EMPTY)
            && $submission->getData()->keyExists($key)
            && !$submission->getData()->fieldEmpty($key)
        ) {
            return false;
        }
        $submission->getData()[$key] = $value;
        return true;
    }

    public function addData(SubmissionInterface $submission)
    {
        $this->configuration = $submission->getConfiguration()->getDataProviderConfiguration(static::getKeyword());
        if ($this->proceed($submission)) {
            $this->process($submission);
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
