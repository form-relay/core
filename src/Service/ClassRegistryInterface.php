<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Route\RouteInterface;

interface ClassRegistryInterface
{
    public function registerConfigurationResolver(string $class, string $interface = '', array $additionalArguments = []);
    public function getConfigurationResolver(string $resolverInterface, string $keyword, $config, ConfigurationResolverContextInterface $context);

    public function registerContentResolver(string $class, array $additionalArguments = []);

    /**
     * @param string $keyword
     * @param mixed $config
     * @param ConfigurationResolverContextInterface $context
     * @return ContentResolverInterface|null
     */
    public function getContentResolver(string $keyword, $config, ConfigurationResolverContextInterface $context);

    public function registerEvaluation(string $class, array $additionalArguments = []);

    /**
     * @param string $keyword
     * @param mixed $config
     * @param ConfigurationResolverContextInterface $context
     * @return EvaluationInterface|null
     */
    public function getEvaluation(string $keyword, $config, ConfigurationResolverContextInterface $context);

    public function registerValueMapper(string $class, array $additionalArguments = []);

    /**
     * @param string $keyword
     * @param mixed $config
     * @param ConfigurationResolverContextInterface $context
     * @return ValueMapperInterface|null
     */
    public function getValueMapper(string $keyword, $config, ConfigurationResolverContextInterface $context);

    public function registerDataProvider(string $class, array $additionalArguments = []);
    public function getDataProviders(): array;
    public function deleteDataProvider(string $class);

    public function registerRoute(string $class, array $additionalArguments = []);
    public function getRoutes(): array;

    /**
     * @param string $routeName
     * @return RouteInterface|null
     */
    public function getRoute(string $routeName);
    public function deleteRoute(string $class);

    public function registerDataDispatcher(string $class, array $additionalArguments = []);

    /**
     * @param string $keyword
     * @return DataDispatcherInterface|null
     */
    public function getDataDispatcher(string $keyword);
    public function deleteDataDispatcher(string $class);
}
