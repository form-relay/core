<?php

namespace FormRelay\Core\Route;

use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Helper\ConfigurationTrait;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Plugin\Plugin;
use FormRelay\Core\Utility\GeneralUtility;

abstract class Route extends Plugin implements RouteInterface
{
    use ConfigurationTrait;

    const KEY_ENABLED = 'enabled';
    const DEFAULT_ENABLED = false;

    const KEY_IGNORE_EMPTY_FIELDS = 'ignoreEmptyFields';
    const DEFAULT_IGNORE_EMPTY_FIELDS = false;

    const KEY_PASSTHROUGH_FIELDS = 'passthroughFields';
    const DEFAULT_PASSTHROUGH_FIELDS = false;

    const KEY_EXCLUDE_FIELDS = 'excludeFields';
    const DEFAULT_EXCLUDE_FIELDS = [];

    const KEY_GATE = 'gate';
    const DEFAULT_GATE = [];

    const KEY_FIELDS = 'fields';
    const DEFAULT_FIELDS = [];

    /** @var SubmissionInterface */
    protected $submission;

    /** @var int */
    protected $pass;

    /** @var array */
    protected $configuration;

    protected function resolveContent($config, $context = null)
    {
        if ($context === null) {
            $context = new ConfigurationResolverContext($this->submission);
        }
        /** @var GeneralContentResolver $contentResolver */
        $contentResolver = $this->registry->getContentResolver('general', $config, $context);
        return $contentResolver->resolve();
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
                $result = $this->resolveContent($value, $baseContext->copy());
                if ($result !== null) {
                    $fields[$key] = $result;
                }
            }
        }

        // ignore empty fields
        if ($this->getConfig(static::KEY_IGNORE_EMPTY_FIELDS)) {
            $fields = array_filter($fields, function ($a) { return !GeneralUtility::isEmpty($a); });
        }

        // exclude specific fields directly
        $excludeFields = $this->getConfig(static::KEY_EXCLUDE_FIELDS);
        GeneralUtility::castValueToArray($excludeFields);
        foreach ($excludeFields as $excludeField) {
            if (array_key_exists($excludeField, $fields)) {
                unset($fields[$excludeField]);
            }
        }

        return $fields;
    }

    protected function processGate(): bool
    {
        $context = new ConfigurationResolverContext($this->submission);
        $evaluation = $this->registry->getEvaluation(
            'gate',
            [
                'key' => $this->getKeyword(),
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
        $this->configuration = $submission->getConfiguration()->getRoutePassConfiguration($this->getKeyword(), $pass);

        if (!$this->processGate()) {
            $this->logger->debug('gate not passed for route "' . $this->getKeyword() . '" in pass ' . $pass . '.');
            return false;
        }
        $data = $this->buildRouteData();
        if (!$data) {
            throw new FormRelayException('no data generated for route "' . $this->getKeyword() . '" in pass ' . $pass . '.');
        }

        $dataDispatcher = $this->getDispatcher();
        if (!$dataDispatcher) {
            throw new FormRelayException('no dispatcher found for route "' . $this->getKeyword() . '" in pass ' . $pass . '.');
        }

        $dataDispatcher->send($data);
        return true;
    }

    public function getPassCount(SubmissionInterface $submission): int
    {
        return $submission->getConfiguration()->getRoutePassCount($this->getKeyword());
    }

    public function addContext(SubmissionInterface $submission, RequestInterface $request, int $pass)
    {
        $this->submission = $submission;
        $this->pass = $pass;
        $this->configuration = $submission->getConfiguration()->getRoutePassConfiguration($this->getKeyword(), $pass);
    }

    /**
     * @return DataDispatcherInterface|null
     */
    abstract protected function getDispatcher();

    public static function getDefaultConfiguration(): array
    {
        return [
            static::KEY_ENABLED => static::DEFAULT_ENABLED,
            static::KEY_IGNORE_EMPTY_FIELDS => static::DEFAULT_IGNORE_EMPTY_FIELDS,
            static::KEY_PASSTHROUGH_FIELDS => static::DEFAULT_PASSTHROUGH_FIELDS,
            static::KEY_EXCLUDE_FIELDS => static::DEFAULT_EXCLUDE_FIELDS,
            static::KEY_GATE => static::DEFAULT_GATE,
            static::KEY_FIELDS => static::DEFAULT_FIELDS,
        ];
    }
}
