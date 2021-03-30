<?php

namespace FormRelay\Core\ConfigurationResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\Service\RegisterableTrait;
use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class ConfigurationResolver implements ConfigurationResolverInterface
{
    use RegisterableTrait;

    /** @var RegistryInterface */
    protected $registry;

    /** @var array|string */
    protected $config;

    protected $context;

    /**
     * ConfigurationResolver constructor.
     * @param RegistryInterface $registry
     * @param array|string $config
     * @param ConfigurationResolverContextInterface $context
     */
    public function __construct(RegistryInterface $registry, $config, ConfigurationResolverContextInterface $context)
    {
        $this->registry = $registry;
        $this->context = $context;
        if ($this->ignoreScalarConfig() && !is_array($config)) {
            $this->config = [];
        } elseif ($this->convertScalarConfigToArray() && !is_array($config)) {
            $this->config = GeneralUtility::castValueToArray($config);
        } else {
            $this->config = $config;
        }
    }
    
    protected function resolveForeignKeyword(string $resolverInterface, string $keyword, $config, ConfigurationResolverContextInterface $context = null)
    {
        if ($context === null) {
            $context = $this->context->copy();
        }
        return $this->registry->getConfigurationResolver($resolverInterface, $keyword, $config, $context);
    }

    /**
     * @param string $keyword
     * @param array|string $config
     * @param ConfigurationResolverContextInterface $context
     * @return ConfigurationResolverInterface|null
     */
    protected function resolveKeyword(string $keyword, $config, ConfigurationResolverContextInterface $context = null)
    {
        return $this->resolveForeignKeyword(static::getResolverInterface(), $keyword, $config, $context);
    }

    abstract protected static function getResolverInterface(): string;

    public static function getClassType(): string
    {
        if (defined(static::class . '::RESOLVER_TYPE')) {
            return static::RESOLVER_TYPE;
        }
        return '';
    }

    protected function sortSubResolvers(array &$subResolvers)
    {
        ksort($subResolvers, SORT_NUMERIC);
        usort($subResolvers, function(ConfigurationResolverInterface $a, ConfigurationResolverInterface $b) {
            if ($a->getWeight() === $b->getWeight()) {
                return 0;
            };
            return $a->getWeight() < $b->getWeight() ? -1 : 1;
        });
    }

    /**
     * determines if the configuration should be an empty array if the passed config is a scalar value
     * this is useful for configurations like:
     * field.appendValue = 1
     * ... which can (but does not have to) have a configuration like:
     * field.appendValue.separator = \n
     *
     * @return boolean
     */
    protected function ignoreScalarConfig()
    {
        return false;
    }

    /**
     * determines if the configuration should be converted to an array (explode) if it is a scalar value
     * this is useful for configurations like:
     * gate.required = field_a,field_b,field_c
     * ... which can also be expressed like:
     * gate.required {
     *     1 = field_a
     *     2 = field_b
     *     3 = field_c
     * }
     *
     * @return bool
     */
    protected function convertScalarConfigToArray()
    {
        return false;
    }

    protected function fieldExists($key, bool $markAsProcessed = true): bool
    {
        if ($markAsProcessed) {
            $this->context['tracker']->markAsProcessed($key);
        }
        return array_key_exists($key, $this->context['data']);
    }

    protected function getFieldValue($key, bool $markAsProcessed = true)
    {
        $fieldValue = $this->fieldExists($key, $markAsProcessed)
            ? $this->context['data'][$key]
            : null;
        return $fieldValue;
    }
    
    protected function resolveContent($config, ConfigurationResolverContextInterface $context = null)
    {
        /** @var GeneralContentResolver $contentResolver */
        $contentResolver = $this->resolveForeignKeyword(ContentResolverInterface::class, 'general', $config, $context);
        return $contentResolver->resolve();
    }

    protected function resolveValueMap($config, $value, ConfigurationResolverContextInterface $context = null)
    {
        /** @var GeneralValueMapper $valueMapper */
        $valueMapper = $this->resolveForeignKeyword(ValueMapperInterface::class, 'general', $config, $context);
        return $valueMapper->resolve($value);
    }

    protected function resolveEvaluation($config, ConfigurationResolverContextInterface $context = null)
    {
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->resolveForeignKeyword(EvaluationInterface::class, 'general', $config, $context);
        return $evaluation->resolve();
    }

    protected function evaluate($config, ConfigurationResolverContextInterface $context = null)
    {
        $evaluation = $this->resolveForeignKeyword(EvaluationInterface::class, 'general', $config, $context);
        return $evaluation->eval();
    }
}
