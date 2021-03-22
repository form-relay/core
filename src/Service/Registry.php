<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\DataProvider\DataProviderInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Exception\RegistryException;
use FormRelay\Core\Factory\LoggerFactoryInterface;
use FormRelay\Core\Factory\NullLoggerFactory;
use FormRelay\Core\Factory\QueueDataFactory;
use FormRelay\Core\Factory\QueueDataFactoryInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Queue\NonPersistentQueue;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Request\DefaultRequest;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Route\RouteInterface;

class Registry implements RegistryInterface
{
    protected $configurationResolverClasses = [];
    protected $routeClasses = [];
    protected $dataProviderClasses = [];
    protected $dataDispatcherClasses = [];

    protected $additionalArgumentsPerClass = [];

    protected $request;
    protected $loggerFactory;
    protected $queue;
    protected $queueDataFactory;

    public function __construct(
        RequestInterface $request = null,
        LoggerFactoryInterface $loggerFactory = null,
        QueueInterface $queue = null,
        QueueDataFactoryInterface $queueDataFactory = null
    ) {
        $this->request = $request ?? new DefaultRequest();
        $this->loggerFactory = $loggerFactory ?? new NullLoggerFactory();
        $this->queue = $queue ?? new NonPersistentQueue();
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

    public function getQueueDataFactory(): QueueDataFactoryInterface
    {
        return $this->queueDataFactory;
    }

    protected function get(string $class, array $arguments = [])
    {
        if (isset($this->additionalArgumentsPerClass[$class])) {
            foreach ($this->additionalArgumentsPerClass[$class] as $argument) {
                $arguments[] = $argument;
            }
        }
        return new $class(...$arguments);
    }

    protected function classValidation($class, $interface)
    {
        if (!in_array($interface, class_implements($class))) {
            throw new RegistryException('class "' . $class . '" has to implement interface "' . $interface . '".');
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

    protected function sortRegisterables(array &$registerables)
    {
        uasort($registerables, function (RegisterableInterface $a, RegisterableInterface $b) {
            if ($a->getWeight() === $b->getWeight()) {
                return 0;
            }
            return $a->getWeight() < $b->getWeight() ? -1 : 1;
        });
    }

    public function registerConfigurationResolver(string $class, string $interface = '', array $additionalArguments = [])
    {
        $this->classValidation($class, $interface ?: ConfigurationResolverInterface::class);
        $this->configurationResolverClasses[$class::getClassType()][$class::getKeyword()] = $class;
        if (!empty($additionalArguments)) {
            $this->additionalArgumentsPerClass[$class] = $additionalArguments;
        } else {
            unset($this->additionalArgumentsPerClass[$class]);
        }
    }

    public function registerEvaluation(string $class, array $additionalArguments = [])
    {
        $this->registerConfigurationResolver($class, EvaluationInterface::class, $additionalArguments);
    }

    public function registerContentResolver(string $class, array $additionalArguments = [])
    {
        $this->registerConfigurationResolver($class, ContentResolverInterface::class, $additionalArguments);
    }

    public function registerValueMapper(string $class, array $additionalArguments = [])
    {
        $this->registerConfigurationResolver($class, ValueMapperInterface::class, $additionalArguments);
    }

    public function getConfigurationResolver(string $resolverInterface, string $keyword, $config, ConfigurationResolverContextInterface $context)
    {
        $this->classValidation($resolverInterface, ConfigurationResolverInterface::class);
        $resolverType = $resolverInterface::RESOLVER_TYPE;
        if (isset($this->configurationResolverClasses[$resolverType][$keyword])) {
            return $this->get($this->configurationResolverClasses[$resolverType][$keyword], [$this, $config, $context]);
        }
        if ($this->checkKeywordAsClass($keyword, $resolverInterface)) {
            return $this->get($keyword, [$this, $config, $context]);
        }
        return null;
    }

    public function getEvaluation(string $keyword, $config, ConfigurationResolverContextInterface $context)
    {
        return $this->getConfigurationResolver(EvaluationInterface::class, $keyword, $config, $context);
    }

    public function getContentResolver(string $keyword, $config, ConfigurationResolverContextInterface $context)
    {
        return $this->getConfigurationResolver(ContentResolverInterface::class, $keyword, $config, $context);
    }

    public function getValueMapper(string $keyword, $config, ConfigurationResolverContextInterface $context)
    {
        return $this->getConfigurationResolver(ValueMapperInterface::class, $keyword, $config, $context);
    }

    public function getRoutes(): array
    {
        $routes = [];
        foreach ($this->routeClasses as $routeClass) {
            $routes[$routeClass::getKeyword()] = $this->get($routeClass, [$this]);
        }
        $this->sortRegisterables($routes);
        return $routes;
    }

    public function getRoute(string $routeName)
    {
        $route = null;
        foreach ($this->routeClasses as $routeClass) {
            if ($routeClass::getKeyword() === $routeName) {
                $route = $this->get($routeClass, [$this]);
                break;
            }
        }
        return $route;
    }

    public function registerRoute(string $class, array $additionalArguments = [])
    {
        $this->classValidation($class, RouteInterface::class);
        $this->routeClasses[$class::getKeyword()] = $class;
        if (!empty($additionalArguments)) {
            $this->additionalArgumentsPerClass[$class] = $additionalArguments;
        } else {
            unset($this->additionalArgumentsPerClass[$class]);
        }
    }

    public function deleteRoute(string $class)
    {
        $this->classValidation($class, RouteInterface::class);
        unset($this->routeClasses[$class::getKeyword()]);
    }

    public function getRouteDefaultConfigurations(): array
    {
        $result = [];
        foreach ($this->routeClasses as $key => $class) {
            $result[$key] = $class::getDefaultConfiguration();
        }
        return $result;
    }

    public function getDataProviders(): array
    {
        $dataProviders = [];
        foreach ($this->dataProviderClasses as $dataProviderClass) {
            $dataProviders[$dataProviderClass::getKeyword()] = $this->get($dataProviderClass, [$this]);
        }
        $this->sortRegisterables($dataProviders);
        return $dataProviders;
    }

    public function registerDataProvider(string $class, array $additionalArguments = [])
    {
        $this->classValidation($class, DataProviderInterface::class);
        $this->dataProviderClasses[$class::getKeyword()] = $class;
        if (!empty($additionalArguments)) {
            $this->additionalArgumentsPerClass[$class] = $additionalArguments;
        } else {
            unset($this->additionalArgumentsPerClass[$class]);
        }
    }

    public function deleteDataProvider(string $class)
    {
        $this->classValidation($class, DataProviderInterface::class);
        unset($this->dataProviderClasses[$class::getKeyword()]);
    }

    public function getDataProviderDefaultConfigurations(): array
    {
        $result = [];
        foreach ($this->dataProviderClasses as $key => $class) {
            $result[$key] = $class::getDefaultConfiguration();
        }
        return $result;
    }

    public function registerDataDispatcher(string $class, array $additionalArguments = [])
    {
        $this->classValidation($class, DataDispatcherInterface::class);
        $this->dataDispatcherClasses[$class::getKeyword()] = $class;
        if (!empty($additionalArguments)) {
            $this->additionalArgumentsPerClass[$class] = $additionalArguments;
        } else {
            unset($this->additionalArgumentsPerClass[$class]);
        }
    }

    /**
     * @param string $keyword
     * @return DataDispatcherInterface|null
     */
    public function getDataDispatcher(string $keyword)
    {
        $class = null;
        if (isset($this->dataDispatcherClasses[$keyword])) {
            $class = $this->dataDispatcherClasses[$keyword];
        }
        if ($class !== null) {
            return $this->get($class, [$this]);
        }
        return null;
    }

    public function deleteDataDispatcher(string $class)
    {
        $this->classValidation($class, DataDispatcherInterface::class);
        unset($this->dataDispatcherClasses[$class::getKeyword()]);
    }

    public function getGlobalDefaultConfiguration(): array
    {
        return [
            'async' => false,
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
