<?php

namespace FormRelay\Core\Route;

use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use FormRelay\Core\ConfigurationResolver\FieldMapper\GeneralFieldMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class Route implements RouteInterface
{
    /** @var RegistryInterface */
    protected $registry;

    /** @var LoggerInterface */
    protected $logger;

    /** @var RequestInterface */
    protected $request;

    /** @var SubmissionInterface */
    protected $submission;

    /** @var int */
    protected $currentPass;

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
        if (array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        }
        return $default;
    }

    protected function buildDefaults()
    {
        $defaultsConfig = $this->configuration['defaults'] ?? [];
        $defaults = [];
        foreach ($defaultsConfig as $key => $value) {
            $context = new ConfigurationResolverContext($this->submission);
            /** @var GeneralContentResolver $contentResolver */
            $contentResolver = $this->registry->getContentResolver('general', $value, $context);
            $result = $contentResolver->resolve();
            if ($result !== null) {
                $defaults[$key] = $result;
            }
        }
        return $defaults;
    }

    protected function ignoreField($key, $value)
    {
        if (isset($this->configuration['fields']['ignore'])) {
            $ignoreKeys = explode(',', $this->configuration['fields']['ignore']);
            if (in_array($key, $ignoreKeys)) {
                return true;
            }
        }

        $ignoreIfEmpty = isset($this->configuration['values']['ignoreIfEmpty']) ? !!$this->configuration['values']['ignoreIfEmpty'] : false;
        if ($ignoreIfEmpty && trim($value) === '') {
            return true;
        }

        return false;
    }

    protected function mapValue($key)
    {
        $valueMapping = isset($this->configuration['values']['mapping']) ? $this->configuration['values']['mapping'] : '';
        $context = new ConfigurationResolverContext($this->submission, ['key' => $key]);
        /** @var GeneralValueMapper $valueMapper */
        $valueMapper = $this->registry->getValueMapper('general', isset($valueMapping[$key]) ? $valueMapping[$key] : [], $context);
        return $valueMapper->resolve();
    }

    protected function mapField($key, $value, &$result)
    {
        $fieldMapping = isset($this->configuration['fields']['mapping'][$key])
            ? $this->configuration['fields']['mapping'][$key]
            : $this->configuration['fields']['unmapped'];
        $context = new ConfigurationResolverContext($this->submission, ['key' => $key, 'value' => $value]);
        /** @var GeneralFieldMapper $fieldMapper */
        $fieldMapper = $this->registry->getFieldMapper('general', $fieldMapping, $context);
        $result = $fieldMapper->resolve($result);
        return $result;
    }

    protected function buildRouteData(): array
    {
        $result = $this->buildDefaults();
        foreach ($this->submission->getData() as $key => $value) {
            if ($this->ignoreField($key, $value)) {
                continue;
            }

            $mappedValue = $this->mapValue($key);
            $result = $this->mapField($key, $mappedValue, $result);
        }
        return $result;
    }

    protected function processGate(): bool
    {
        $context = new ConfigurationResolverContext($this->submission);
        $evaluation = $this->registry->getEvaluation(
            'gate',
            [
                'key' => static::getKeyword(),
                'pass' => $this->currentPass
            ],
            $context
        );
        return $evaluation->eval();
    }

    protected function processPass(): bool
    {
        if (!$this->processGate()) {
            $this->logger->debug('gate not passed for route "' . static::getKeyword() . '" in pass ' . $this->currentPass . '.');
            return false;
        }
        $data = $this->buildRouteData();
        if (!$data) {
            $this->logger->debug('no data generated for route "' . static::getKeyword() . '" in pass ' . $this->currentPass . '.');
            return false;
        }

        $dataDispatcher = $this->getDispatcher();
        if (!$dataDispatcher) {
            $this->logger->debug('no dispatcher found for route "' . static::getKeyword() . '" in pass ' . $this->currentPass . '.');
            return false;
        }

        return $dataDispatcher->send($data);
    }

    public function process(SubmissionInterface $submission): bool
    {
        $this->submission = $submission;
        $result = false;
        $passCount = $submission->getConfiguration()->getRoutePassCount(static::getKeyword());
        for ($pass = 0; $pass < $passCount; $pass++) {
            $this->currentPass = $pass;
            $this->configuration = $submission->getConfiguration()->getRoutePassConfiguration(static::getKeyword(), $pass);
            $result = $this->processPass() || $result;
        }
        return $result;
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
            'enabled' => false,
            'gate' => [],
            'defaults' => [],
            'fields' => [
                'ignore' => '',
                'mapping' => [],
                'unmapped' => null,
            ],
            'values' => [
                'ignoreIfEmpty' => true,
                'mapping' => [],
            ],
        ];
    }
}
