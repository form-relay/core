<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\DataProvider\DataProviderInterface;
use FormRelay\Core\Exception\RegistryException;
use FormRelay\Core\Factory\LoggerFactoryInterface;
use FormRelay\Core\Factory\NullLoggerFactory;
use FormRelay\Core\Factory\QueueDataFactory;
use FormRelay\Core\Factory\QueueDataFactoryInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Queue\NonPersistentQueue;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Queue\QueueProcessor;
use FormRelay\Core\Queue\QueueProcessorInterface;
use FormRelay\Core\Queue\WorkerInterface;
use FormRelay\Core\Plugin\PluginInterface;
use FormRelay\Core\Request\DefaultRequest;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Route\RouteInterface;
use FormRelay\Core\Utility\GeneralUtility;

class Registry implements RegistryInterface
{
    protected $request;
    protected $loggerFactory;
    protected $queue;
    protected $temporaryQueue;
    protected $queueDataFactory;

    protected $pluginClasses = [];
    protected $pluginAdditionalArguments = [];

    public function __construct(
        RequestInterface $request = null,
        LoggerFactoryInterface $loggerFactory = null,
        QueueInterface $queue = null,
        QueueInterface $temporaryQueue = null,
        QueueDataFactoryInterface $queueDataFactory = null
    ) {
        $this->request = $request ?? new DefaultRequest();
        $this->loggerFactory = $loggerFactory ?? new NullLoggerFactory();
        $this->queue = $queue ?? new NonPersistentQueue();
        $this->temporaryQueue = $temporaryQueue ?? new NonPersistentQueue();
        $this->queueDataFactory = $queueDataFactory ?? new QueueDataFactory();
    }

    public function getLogger(string $forClass): LoggerInterface
    {
        return $this->loggerFactory->getLogger($forClass);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }

    public function getTemporaryQueue(): QueueInterface
    {
        return $this->temporaryQueue;
    }

    public function getQueueDataFactory(): QueueDataFactoryInterface
    {
        return $this->queueDataFactory;
    }

    public function getQueueProcessor(QueueInterface $queue, WorkerInterface $worker): QueueProcessorInterface
    {
        return new QueueProcessor($queue, $worker);
    }

    protected function get(string $keyword, string $interface, array $arguments = [])
    {
        $class = $this->pluginClasses[$interface][$keyword] ?? null;
        $additionalArguments = $this->pluginAdditionalArguments[$interface][$keyword] ?? [];

        if (!$class || !class_exists($class)) {
            $class = null;
            if ($this->checkKeywordAsClass($keyword, $interface)) {
                $class = $keyword;
                $keyword = GeneralUtility::getPluginKeyword($keyword, $interface) ?: $keyword;
                $additionalArguments = [];
            }
        }

        if ($class) {
            array_unshift($arguments, $this->getLogger($class));
            array_unshift($arguments, $this);
            array_unshift($arguments, $keyword);
            array_push($arguments, ...$additionalArguments);
            return new $class(...$arguments);
        }

        return null;
    }

    protected function getAll(string $interface, array $arguments = [])
    {
        $result = [];
        foreach (array_keys($this->pluginClasses[$interface] ?? []) as $keyword) {
            $result[$keyword] = $this->get($keyword, $interface, $arguments);
        }
        $this->sortPlugins($result);
        return $result;
    }

    protected function register(string $interface, string $class, array $additionalArguments = [], string $keyword = '')
    {
        if (!$keyword || is_numeric($keyword)) {
            $keyword = GeneralUtility::getPluginKeyword($class, $interface) ?: $keyword;
        }
        $this->interfaceValidation($interface, PluginInterface::class);
        $this->classValidation($class, $interface);
        $this->pluginClasses[$interface][$keyword] = $class;
        $this->pluginAdditionalArguments[$interface][$keyword] = $additionalArguments;
    }

    protected function delete(string $keyword, string $interface)
    {
        if (isset($this->pluginClasses[$interface][$keyword])) {
            unset($this->pluginClasses[$interface][$keyword]);
        }
        if (isset($this->pluginAdditionalArguments[$interface][$keyword])) {
            unset($this->pluginAdditionalArguments[$interface][$keyword]);
        }
    }

    protected function classValidation(string $class, string $interface)
    {
        if (!class_exists($class)) {
            throw new RegistryException('class "' . $class . '" does not exist.');
        }
        if (!in_array($interface, class_implements($class))) {
            throw new RegistryException('class "' . $class . '" has to implement interface "' . $interface . '".');
        }
    }

    protected function interfaceValidation(string $interface, string $parentInterface)
    {
        if (!interface_exists($interface)) {
            throw new RegistryException('interface "' . $interface . '" does not exist.');
        }
        if (!is_subclass_of($interface, $parentInterface, true)) {
            throw new RegistryException('interface "' . $interface . '" has to extend "' . $parentInterface . '".');
        }
    }

    protected function checkKeywordAsClass(string $keyword, string $interface): bool
    {
        $result = false;
        if (class_exists($keyword)) {
            try {
                $this->classValidation($keyword, $interface);
                $result = true;
            } catch (RegistryException $e) {
                // keyword is not a class (or it is not implementing the desired interface)
            }
        }
        return $result;
    }

    protected function sortPlugins(array &$plugins)
    {
        uasort($plugins, function (PluginInterface $a, PluginInterface $b) {
            return $a->getWeight() <=> $b->getWeight();
        });
    }

    public function registerConfigurationResolver(string $interface, string $class, array $additionalArguments = [], string $keyword = '')
    {
        $this->interfaceValidation($interface, ConfigurationResolverInterface::class);
        $this->classValidation($class, $interface);
        $this->register($interface, $class, $additionalArguments, $keyword);
    }

    public function registerEvaluation(string $class, array $additionalArguments = [], string $keyword = '')
    {
        $this->registerConfigurationResolver(EvaluationInterface::class, $class, $additionalArguments, $keyword);
    }

    public function registerContentResolver(string $class, array $additionalArguments = [], string $keyword = '')
    {
        $this->registerConfigurationResolver(ContentResolverInterface::class, $class, $additionalArguments, $keyword);
    }

    public function registerValueMapper(string $class, array $additionalArguments = [], string $keyword = '')
    {
        $this->registerConfigurationResolver(ValueMapperInterface::class, $class, $additionalArguments, $keyword);
    }

    public function getConfigurationResolver(string $keyword, string $interface, $config, ConfigurationResolverContextInterface $context)
    {
        $this->interfaceValidation($interface, ConfigurationResolverInterface::class);
        return $this->get($keyword, $interface, [$config, $context]);
    }

    public function getEvaluation(string $keyword, $config, ConfigurationResolverContextInterface $context)
    {
        return $this->getConfigurationResolver($keyword, EvaluationInterface::class, $config, $context);
    }

    public function getContentResolver(string $keyword, $config, ConfigurationResolverContextInterface $context)
    {
        return $this->getConfigurationResolver($keyword, ContentResolverInterface::class, $config, $context);
    }

    public function getValueMapper(string $keyword, $config, ConfigurationResolverContextInterface $context)
    {
        return $this->getConfigurationResolver($keyword, ValueMapperInterface::class, $config, $context);
    }

    public function getRoutes(): array
    {
        return $this->getAll(RouteInterface::class);
    }

    public function getRoute(string $keyword)
    {
        return $this->get($keyword, RouteInterface::class);
    }

    public function registerRoute(string $class, array $additionalArguments = [], string $keyword = '')
    {
        $this->register(RouteInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteRoute(string $keyword)
    {
        $this->delete($keyword, RouteInterface::class);
    }

    public function getRouteDefaultConfigurations(): array
    {
        $result = [];
        foreach ($this->pluginClasses[RouteInterface::class] as $key => $class) {
            $result[$key] = $class::getDefaultConfiguration();
        }
        return $result;
    }

    public function getDataProviders(): array
    {
        return $this->getAll(DataProviderInterface::class);
    }

    public function registerDataProvider(string $class, array $additionalArguments = [], string $keyword = '')
    {
        $this->register(DataProviderInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteDataProvider(string $keyword)
    {
        $this->delete($keyword, DataProviderInterface::class);
    }

    public function getDataProviderDefaultConfigurations(): array
    {
        $result = [];
        foreach ($this->pluginClasses[DataProviderInterface::class] ?? [] as $key => $class) {
            $result[$key] = $class::getDefaultConfiguration();
        }
        return $result;
    }

    public function registerDataDispatcher(string $class, array $additionalArguments = [], string $keyword = '')
    {
        $this->register(DataDispatcherInterface::class, $class, $additionalArguments, $keyword);
    }

    /**
     * @param string $keyword
     * @return DataDispatcherInterface|null
     */
    public function getDataDispatcher(string $keyword)
    {
        return $this->get($keyword, DataDispatcherInterface::class);
    }

    public function deleteDataDispatcher(string $keyword)
    {
        $this->delete($keyword, DataDispatcherInterface::class);
    }

    public function getGlobalDefaultConfiguration(): array
    {
        return [
            'async' => false,
            'disableStorage' => false,
        ];
    }

    public function getDefaultConfiguration(): array
    {
        $defaultConfig = $this->getGlobalDefaultConfiguration();
        $defaultConfig['dataProviders'] = $this->getDataProviderDefaultConfigurations();
        $defaultConfig['routes'] = $this->getRouteDefaultConfigurations();
        return $defaultConfig;
    }
}
