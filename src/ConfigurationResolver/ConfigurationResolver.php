<?php

namespace FormRelay\Core\ConfigurationResolver;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\Service\RegistryInterface;

abstract class ConfigurationResolver implements ConfigurationResolverInterface
{
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
            $this->config = $config ? explode(',', $config) : [];
        } else {
            $this->config = $config;
        }
    }

    /**
     * @param string $keyword
     * @param array|string $config
     * @param ConfigurationResolverContextInterface $context
     * @return ConfigurationResolverInterface|null
     */
    protected function resolveKeyword(string $keyword, $config, ConfigurationResolverContextInterface $context = null)
    {
        if ($context === null) {
            $context = $this->context->copy();
        }
        return $this->registry->getConfigurationResolver(static::getResolverInterface(), $keyword, $config, $context);
    }

    abstract protected static function getResolverInterface(): string;

    public static function getResolverType(): string
    {
        if (defined(static::class . '::RESOLVER_TYPE')) {
            return static::RESOLVER_TYPE;
        }
        return '';
    }

    public static function getKeyword(): string
    {
        $resolverType = static::getResolverType();
        if ($resolverType && preg_match('/([^\\\\]+)' . $resolverType . '$/', static::class, $matches)) {
            return lcfirst($matches[1]);
        }
        return '';
    }

    public function getWeight(): int {
        return 10;
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
}
