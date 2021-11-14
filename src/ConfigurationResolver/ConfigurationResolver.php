<?php

namespace FormRelay\Core\ConfigurationResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\Helper\ConfigurationTrait;
use FormRelay\Core\Helper\RegisterableTrait;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Service\ClassRegistryInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class ConfigurationResolver implements ConfigurationResolverInterface
{
    use RegisterableTrait;
    use ConfigurationTrait;

    /**
     * config is used as is
     */
    const CONFIGURATION_BEHAVIOUR_DEFAULT = 0;

    /**
     * the configuration will be treated as an empty array if the passed config is a scalar value
     * this is useful for configurations like:
     * feature = 1
     * ... which also can (but does not have to) have a configuration like:
     * feature.option = foobar
     */
    const CONFIGURATION_BEHAVIOUR_IGNORE_SCALAR = 1;

    /**
     * the configuration will be used to resolve content
     * which is then cast to an array afterwards
     * - multi-values will be cast to an array
     * - scalar values will be converted (explode)
     * this is useful for configurations like:
     * feature = a,b,c
     * ... which can also be expressed like:
     * feature.list {
     *     1 = a
     *     2 = b
     *     3 = c
     * }
     * ... or even with more complex logic like:
     * feature.list {
     *     1 = a
     *     2.field = field_b
     *     3.if {
     *         foo = bar
     *         then = c
     *         else = c2
     *     }
     * }
     */
    const CONFIGURATION_BEHAVIOUR_RESOLVE_CONTENT_THEN_CAST_TO_ARRAY = 2;

    /**
     * the configuration will be converted to an array if it is a scalar value
     * while the original configuration will be set to the 'self' key within this array
     * which results in the two expressions to be identical:
     * feature = foo
     * feature {
     *     self = foo
     * }
     */
    const CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE = 3;

    /** @var ClassRegistryInterface */
    protected $registry;

    /** @var array|string */
    protected $configuration;

    protected $context;

    /**
     * ConfigurationResolver constructor.
     * @param ClassRegistryInterface $registry
     * @param array|string $config
     * @param ConfigurationResolverContextInterface $context
     */
    public function __construct(ClassRegistryInterface $registry, $config, ConfigurationResolverContextInterface $context)
    {
        $this->registry = $registry;
        $this->context = $context;
        $this->configuration = $config;

        switch ($this->getConfigurationBehaviour()) {
            case static::CONFIGURATION_BEHAVIOUR_IGNORE_SCALAR:
                if (!is_array($config)) {
                    $this->configuration = [];
                }
                break;
            case static::CONFIGURATION_BEHAVIOUR_RESOLVE_CONTENT_THEN_CAST_TO_ARRAY:
                $this->configuration = GeneralUtility::castValueToArray(
                    $this->resolveContent($this->configuration)
                );
                break;
            case static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE:
                if (!is_array($config)) {
                    $this->configuration = [SubmissionConfigurationInterface::KEY_SELF => $this->configuration];
                }
                break;
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
        usort($subResolvers, function (ConfigurationResolverInterface $a, ConfigurationResolverInterface $b) {
            if ($a->getWeight() === $b->getWeight()) {
                return 0;
            }
            return $a->getWeight() < $b->getWeight() ? -1 : 1;
        });
    }

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_DEFAULT;
    }

    protected function fieldExists($key, bool $markAsProcessed = true): bool
    {
        if ($markAsProcessed) {
            $this->context->getFieldTracker()->markAsProcessed($key);
        }
        return $this->context->getData()->fieldExists($key);
    }

    protected function getFieldValue($key, bool $markAsProcessed = true)
    {
        $fieldValue = $this->fieldExists($key, $markAsProcessed)
            ? $this->context->getData()[$key]
            : null;
        return $fieldValue;
    }

    /**
     * @param mixed $key
     * @param ConfigurationResolverContextInterface|null $context
     */
    protected function addKeyToContext($key, $context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        $resolvedKey = $this->resolveContent($key);
        if (!GeneralUtility::isEmpty($resolvedKey)) {
            $context['key'] = $resolvedKey;
        } elseif (isset($context['key'])) {
            unset($context['key']);
        }
        if (isset($context['index'])) {
            unset($context['index']);
        }
    }

    /**
     * @param mixed $index
     * @param ConfigurationResolverContextInterface|null $context
     */
    protected function addIndexToContext($index, $context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        $resolvedKey = $this->resolveContent($index);
        if (!GeneralUtility::isEmpty($resolvedKey)) {
            $index = $this->getIndexFromContext($context);
            $index[] = $resolvedKey;
            $context['index'] = $index;
        } elseif (isset($context['index'])) {
            unset($context['index']);
        }
    }

    /**
     * @param ConfigurationResolverContextInterface|null $context
     * @return FieldInterface|string|null
     */
    protected function getKeyFromContext($context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        return $context['key'] ?? '';
    }

    /**
     * @param ConfigurationResolverContextInterface|null $context
     * @return array
     */
    protected function getIndexFromContext($context = null): array
    {
        if ($context === null) {
            $context = $this->context;
        }
        return $context['index'] ?? [];
    }

    /**
     * Fetching the value of the previously selected field (and index).
     * Examples:
     * field.country // === getFieldValue(country)
     * field.countries.index.0 // === getFieldValue(countries)[0]
     * field.some_deep_nested_field.index.7.index.5 // === getFieldValue(some_deep_nested_field)[7][5]
     *
     * Can also just return the field name instead of its value. In such a case the index is ignored.
     * Examples:
     * field.country.key = country // a tautology
     * loopData.condition.key.in = country,state // loops over the fields "country" and "state" if they exist
     *
     * @param ConfigurationResolverContextInterface|null $context
     * @return FieldInterface|string|null
     */
    protected function getSelectedValue($context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        $key = $this->getKeyFromContext();
        if ($key) {
            if ($context['useKey'] ?? false) {
                return $key;
            } else {
                $fieldValue = $this->getFieldValue($key);
                $indices = $this->getIndexFromContext($context);
                while (!empty($indices)) {
                    $index = array_shift($indices);
                    if ($fieldValue instanceof MultiValueField) {
                        $fieldValue = $fieldValue[$index] ?? null;
                    } else {
                        $fieldValue = null;
                        break;
                    }
                }
                return $fieldValue;
            }
        }
        return null;
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
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->resolveForeignKeyword(EvaluationInterface::class, 'general', $config, $context);
        return $evaluation->eval();
    }

    public static function getDefaultConfiguration(): array
    {
        return [];
    }
}
