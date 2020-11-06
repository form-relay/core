<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Utility\GeneralUtility;

class SplitFieldMapper extends FieldMapper
{
    const KEY_TOKEN = 'token';
    const DEFAULT_TOKEN = '\\s';

    const KEY_FIELDS = 'fields';

    const DELIMITER = ',';

    /**
     * SplitFieldMapper constructor.
     * @param RegistryInterface $registry
     * @param array|string $config
     * @param ConfigurationResolverContextInterface $context
     */
    public function __construct(RegistryInterface $registry, $config, ConfigurationResolverContextInterface $context)
    {
        if (!is_array($config)) {
            $config = [static::KEY_FIELDS => $config];
        }
        parent::__construct($registry, $config, $context);
    }

    public function finish(array &$result): bool
    {
        $token = GeneralUtility::parseSeparatorString($this->config[static::KEY_TOKEN] ?: static::DEFAULT_TOKEN);
        $splitFields = is_array($this->config[static::KEY_FIELDS])
            ? $this->config[static::KEY_FIELDS]
            : explode(static::DELIMITER, $this->config[static::KEY_FIELDS]);
        $splitValues = explode($token, $this->context['value']);
        while (count($splitFields) > 1 && count($splitValues) > 0) {
            // split for all fields but the last
            $splitField = array_shift($splitFields);
            $splitValue = array_shift($splitValues);
            $subContext = $this->context->copy();
            $subContext['value'] = $splitValue;
            /** @var GeneralFieldMapper $fieldMapper */
            $fieldMapper = $this->resolveKeyword('general', $splitField, $subContext);
            $result = $fieldMapper->resolve($result);
        }
        if (count($splitValues) > 0) {
            // concat the remaining split values again and use them for the last field
            $splitField = array_shift($splitFields);
            $splitValue = implode($token, $splitValues);
            $subContext = $this->context->copy();
            $subContext['value'] = $splitValue;
            /** @var GeneralFieldMapper $fieldMapper */
            $fieldMapper = $this->resolveKeyword('general', $splitField, $subContext);
            $result = $fieldMapper->resolve($result);
        }
        return true;
    }
}
