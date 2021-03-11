<?php

namespace FormRelay\Core\Route;

use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class Route implements RouteInterface
{
    const KEY_ENABLED = 'enabled';
    const DEFAULT_ENABLED = false;

    const KEY_IGNORE_EMPTY_FIELDS = 'ignoreEmptyFields';
    const DEFAULT_IGNORE_EMPTY_FIELDS = false;

    const KEY_PASSTHROUGH_FIELDS = 'passthroughFields';
    const DEFAULT_PASSTHROUGH_FIELDS = false;

    const KEY_GATE = 'gate';
    const DEFAULT_GATE = [];

    const KEY_FIELDS = 'fields';
    const DEFAULT_FIELDS = [];

    /** @var RegistryInterface */
    protected $registry;

    /** @var LoggerInterface */
    protected $logger;

    /** @var RequestInterface */
    protected $request;

    /** @var SubmissionInterface */
    protected $submission;

    /** @var int */
    protected $pass;

    /** @var array */
    protected $configuration;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->request = $registry->getRequest();
        $this->logger = $registry->getLogger(static::class);
    }

    protected function getConfig(string $key, $default = null)
    {
        if ($default === null) {
            $defaults = static::getDefaultConfiguration();
            if (array_key_exists($key, $defaults)) {
                $default = $defaults[$key];
            }
        }
        if (array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        }
        return $default;
    }

    protected function buildRouteData(): array
    {
        $fields = [];
        if ($this->getConfig(static::KEY_PASSTHROUGH_FIELDS)) {
            // pass through all fields as they are
            foreach ($this->submission->getData() as $key => $value) {
                $fields[$key] = $value;
            }
        } else {
            // compute field configuration
            $fieldConfigList = $this->getConfig(static::KEY_FIELDS);
            $baseContext = new ConfigurationResolverContext($this->submission);
            foreach ($fieldConfigList as $key => $value) {
                $context = $baseContext->copy();
                /** @var GeneralContentResolver $contentResolver */
                $contentResolver = $this->registry->getContentResolver('general', $value, $context);
                $result = $contentResolver->resolve();
                if ($result !== null) {
                    $fields[$key] = $result;
                }
            }
        }
        if ($this->getConfig(static::KEY_IGNORE_EMPTY_FIELDS)) {
            $fields = array_filter($fields, function($a) { return strlen((string)$a) > 0; });
        }
        return $fields;
    }

    protected function processGate(): bool
    {
        $context = new ConfigurationResolverContext($this->submission);
        $evaluation = $this->registry->getEvaluation(
            'gate',
            [
                'key' => static::getKeyword(),
                'pass' => $this->pass
            ],
            $context
        );
        return $evaluation->eval();
    }

    public function processPass(SubmissionInterface $submission, int $pass): bool
    {
        $this->submission = $submission;
        $this->pass = $pass;
        $this->configuration = $submission->getConfiguration()->getRoutePassConfiguration(static::getKeyword(), $pass);

        if (!$this->processGate()) {
            $this->logger->debug('gate not passed for route "' . static::getKeyword() . '" in pass ' . $pass . '.');
            return false;
        }
        $data = $this->buildRouteData();
        if (!$data) {
            throw new FormRelayException('no data generated for route "' . static::getKeyword() . '" in pass ' . $pass . '.');
        }

        $dataDispatcher = $this->getDispatcher();
        if (!$dataDispatcher) {
            throw new FormRelayException('no dispatcher found for route "' . static::getKeyword() . '" in pass ' . $pass . '.');
        }

        return $dataDispatcher->send($data);
    }

    public function getPassCount(SubmissionInterface $submission): int
    {
        return $submission->getConfiguration()->getRoutePassCount(static::getKeyword());
    }

    /**
     * @return DataDispatcherInterface|null
     */
    abstract protected function getDispatcher();

    public function getWeight(): int
    {
        return 10;
    }

    public static function getKeyword(): string
    {
        $namespaceParts = explode('\\', static::class);
        if (count($namespaceParts) > 1 && $namespaceParts[0] === 'FormRelay') {
            return GeneralUtility::camel2dashed($namespaceParts[1]);
        }
        return '';
    }


    public static function getDefaultConfiguration(): array
    {
        return [
            static::KEY_ENABLED => static::DEFAULT_ENABLED,
            static::KEY_IGNORE_EMPTY_FIELDS => static::DEFAULT_IGNORE_EMPTY_FIELDS,
            static::KEY_PASSTHROUGH_FIELDS => static::DEFAULT_PASSTHROUGH_FIELDS,
            static::KEY_GATE => static::DEFAULT_GATE,
            static::KEY_FIELDS => static::DEFAULT_FIELDS,
        ];
    }
}
