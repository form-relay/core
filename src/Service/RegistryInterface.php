<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Request\RequestInterface;

interface RegistryInterface
{
    public function getRequest(): RequestInterface;
    public function getLogger(string $forClass): LoggerInterface;

    public function registerConfigurationResolver(string $class);
    public function getConfigurationResolver(string $resolverInterface, string $keyword, $config, ConfigurationResolverContextInterface $context);

    public function registerContentResolver(string $class);

    /**
     * @param string $keyword
     * @param $config
     * @param ConfigurationResolverContextInterface $context
     * @return ContentResolverInterface|null
     */
    public function getContentResolver(string $keyword, $config, ConfigurationResolverContextInterface $context);

    public function registerEvaluation(string $class);

    /**
     * @param string $keyword
     * @param $config
     * @param ConfigurationResolverContextInterface $context
     * @return EvaluationInterface|null
     */
    public function getEvaluation(string $keyword, $config, ConfigurationResolverContextInterface $context);

    public function registerValueMapper(string $class);

    /**
     * @param string $keyword
     * @param $config
     * @param ConfigurationResolverContextInterface $context
     * @return ValueMapperInterface|null
     */
    public function getValueMapper(string $keyword, $config, ConfigurationResolverContextInterface $context);

    public function registerDataProvider(string $class);
    public function getDataProviders(): array;
    public function deleteDataProvider(string $class);

    public function getDataProviderDefaultConfigurations(): array;

    public function registerRoute(string $class);
    public function getRoutes(): array;
    public function deleteRoute(string $class);

    public function getRouteDefaultConfigurations(): array;

    public function registerDataDispatcher(string $class);

    /**
     * @param string $keyword
     * @param mixed ...$arguments
     * @return DataDispatcherInterface|null
     */
    public function getDataDispatcher(string $keyword, ...$arguments);
    public function deleteDataDispatcher(string $class);

    public function getDefaultConfiguration(): array;
}
