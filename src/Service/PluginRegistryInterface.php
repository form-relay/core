<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Route\RouteInterface;

interface PluginRegistryInterface
{
    /**
     * @param string $interface
     * @param string $class
     * @param array $additionalArguments
     * @param string $keyword
     */
    public function registerConfigurationResolver(string $interface, string $class, array $additionalArguments = [], string $keyword = '');

    /**
     * @param string $keyword
     * @param string $interface
     * @param mixed $config
     * @param ConfigurationResolverContextInterface $context
     * @return ConfigurationResolverInterface|ContentResolverInterface|EvaluationInterface|ValueMapperInterface|null
     */
    public function getConfigurationResolver(string $keyword, string $interface, $config, ConfigurationResolverContextInterface $context);

    /**
     * @param string $class
     * @param array $additionalArguments
     * @param string $keyword
     */
    public function registerContentResolver(string $class, array $additionalArguments = [], string $keyword = '');

    /**
     * @param string $keyword
     * @param mixed $config
     * @param ConfigurationResolverContextInterface $context
     * @return ContentResolverInterface|null
     */
    public function getContentResolver(string $keyword, $config, ConfigurationResolverContextInterface $context);

    /**
     * @param string $class
     * @param array $additionalArguments
     * @param string $keyword
     */
    public function registerEvaluation(string $class, array $additionalArguments = [], string $keyword = '');

    /**
     * @param string $keyword
     * @param mixed $config
     * @param ConfigurationResolverContextInterface $context
     * @return EvaluationInterface|null
     */
    public function getEvaluation(string $keyword, $config, ConfigurationResolverContextInterface $context);

    /**
     * @param string $class
     * @param array $additionalArguments
     * @param string $keyword
     */
    public function registerValueMapper(string $class, array $additionalArguments = [], string $keyword = '');

    /**
     * @param string $keyword
     * @param mixed $config
     * @param ConfigurationResolverContextInterface $context
     * @return ValueMapperInterface|null
     */
    public function getValueMapper(string $keyword, $config, ConfigurationResolverContextInterface $context);

    /**
     * @param string $class
     * @param array $additionalArguments
     * @param string $keyword
     */
    public function registerDataProvider(string $class, array $additionalArguments = [], string $keyword = '');

    /**
     * @return array
     */
    public function getDataProviders(): array;

    /**
     * @param string $keyword
     */
    public function deleteDataProvider(string $keyword);

    /**
     * @param string $class
     * @param array $additionalArguments
     * @param string $keyword
     */
    public function registerRoute(string $class, array $additionalArguments = [], string $keyword = '');

    /**
     * @return array
     */
    public function getRoutes(): array;

    /**
     * @param string $keyword
     * @return RouteInterface|null
     */
    public function getRoute(string $keyword);

    /**
     * @param string $keyword
     */
    public function deleteRoute(string $keyword);

    /**
     * @param string $class
     * @param array $additionalArguments
     * @param string $keyword
     */
    public function registerDataDispatcher(string $class, array $additionalArguments = [], string $keyword = '');

    /**
     * @param string $keyword
     * @return DataDispatcherInterface|null
     */
    public function getDataDispatcher(string $keyword);

    /**
     * @param string $keyword
     */
    public function deleteDataDispatcher(string $keyword);
}
