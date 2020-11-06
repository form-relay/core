<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ContentResolver\ContentResolverInterface;
use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use FormRelay\Core\ConfigurationResolver\Evaluation\EvaluationInterface;
use FormRelay\Core\ConfigurationResolver\FieldMapper\FieldMapperInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\ValueMapperInterface;
use FormRelay\Core\DataProvider\DataProviderInterface;
use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Exception\RegistryException;
use FormRelay\Core\Log\NullLogger;
use FormRelay\Core\Route\RouteInterface;

class Registry implements RegistryInterface
{
    protected $configurationResolverClasses = [];
    protected $routeClasses = [];
    protected $dataProviderClasses = [];
    protected $dataDispatcherClasses = [];

    protected function getLogger(string $forClass)
    {
        return new NullLogger();
    }

    protected function get(string $class, array $arguments = [])
    {
        return new $class(...$arguments);
    }

    protected function classValidation($class, $interface)
    {
        if (!in_array($interface, class_implements($class))) {
            throw new RegistryException('class "' . $class . '" has to imlpement interface "' . $interface . '".');
        }
    }

    protected function sortRegisterables(array &$registerables)
    {
        usort($registerables, function (RegisterableInterface $a, RegisterableInterface $b) {
            if ($a->getWeight() === $b->getWeight()) {
                return 0;
            }
            return $a->getWeight() < $b->getWeight() ? -1 : 1;
        });
    }

    public function registerConfigurationResolver(string $class)
    {
        $this->classValidation($class, ConfigurationResolverInterface::class);
        $this->configurationResolverClasses[$class::getResolverType()][$class::getKeyword()] = $class;
    }

    public function registerEvaluation(string $class)
    {
        $this->classValidation($class, EvaluationInterface::class);
        $this->configurationResolverClasses[EvaluationInterface::RESOLVER_TYPE][$class::getKeyword()] = $class;
    }

    public function registerContentResolver(string $class)
    {
        $this->classValidation($class, ContentResolverInterface::class);
        $this->configurationResolverClasses[ContentResolverInterface::RESOLVER_TYPE][$class::getKeyword()] = $class;
    }

    public function registerFieldMapper(string $class)
    {
        $this->classValidation($class, FieldMapperInterface::class);
        $this->configurationResolverClasses[FieldMapperInterface::RESOLVER_TYPE][$class::getKeyword()] = $class;
    }

    public function registerValueMapper(string $class)
    {
        $this->classValidation($class, ValueMapperInterface::class);
        $this->configurationResolverClasses[ValueMapperInterface::RESOLVER_TYPE][$class::getKeyword()] = $class;
    }

    public function getConfigurationResolver(string $resolverInterface, string $keyword, $config, ConfigurationResolverContextInterface $context)
    {
        $this->classValidation($resolverInterface, ConfigurationResolverInterface::class);
        $resolverType = $resolverInterface::RESOLVER_TYPE;
        if (isset($this->configurationResolverClasses[$resolverType][$keyword])) {
            return $this->get($this->configurationResolverClasses[$resolverType][$keyword], [$this, $config, $context]);
        }
        if (class_exists($keyword) && $this->classValidation($keyword, $resolverInterface)) {
            return $this->get($keyword, [$this, $config, $context]);
        }
        return null;
    }

    public function getEvaluation(string $keyword, $config, ConfigurationResolverContextInterface $context): EvaluationInterface
    {
        return $this->getConfigurationResolver(EvaluationInterface::class, $keyword, $config, $context);
    }

    public function getFieldMapper(string $keyword, $config, ConfigurationResolverContextInterface $context): FieldMapperInterface
    {
        return $this->getConfigurationResolver(FieldMapperInterface::class, $keyword, $config, $context);
    }

    public function getContentResolver(string $keyword, $config, ConfigurationResolverContextInterface $context): ContentResolverInterface
    {
        return $this->getConfigurationResolver(ContentResolverInterface::class, $keyword, $config, $context);
    }

    public function getValueMapper(string $keyword, $config, ConfigurationResolverContextInterface $context): ValueMapperInterface
    {
        return $this->getConfigurationResolver(ValueMapperInterface::class, $keyword, $config, $context);
    }

    public function getRoutes(): array
    {
        $routes = [];
        foreach ($this->routeClasses as $routeClass) {
            $routes[] = $this->get($routeClass, [$this, $this->getLogger($routeClass)]);
        }
        $this->sortRegisterables($routes);
        return $routes;
    }

    public function registerRoute(string $class)
    {
        $this->classValidation($class, RouteInterface::class);
        $this->routeClasses[$class::getKeyword()] = $class;
    }

    public function deleteRoute(string $class)
    {
        $this->classValidation($class, RouteInterface::class);
        unset($this->routeClasses[$class::getKeyword()]);
    }

    public function getDataProviders(): array
    {
        $dataProviders = [];
        foreach ($this->dataProviderClasses as $dataProviderClass) {
            $dataProviders[] = $this->get($dataProviderClass, [$this, $this->getLogger($dataProviderClass)]);
        }
        $this->sortRegisterables($dataProviders);
        return $dataProviders;
    }

    public function registerDataProvider(string $class)
    {
        $this->classValidation($class, DataProviderInterface::class);
        $this->dataProviderClasses[$class::getKeyword()] = $class;
    }

    public function deleteDataProvider(string $class)
    {
        $this->classValidation($class, DataProviderInterface::class);
        unset($this->dataProviderClasses[$class::getKeyword()]);
    }

    public function registerDataDispatcher(string $class)
    {
        $this->classValidation($class, DataDispatcherInterface::class);
        $this->dataDispatcherClasses[$class::getKeyword()] = $class;
    }

    public function getDataDispatcher(string $keyword, ...$arguments): DataDispatcherInterface
    {
        $class = null;
        if (isset($this->dataDispatcherClasses[$keyword])) {
            $class = $this->dataDispatcherClasses[$keyword];
        }
        if (class_exists($keyword) && $this->classValidation($keyword, DataDispatcherInterface::class)) {
            $class = $keyword;
        }
        if ($class !== null) {
            $args = [$this, $this->getLogger($class)];
            foreach ($arguments as $argument) {
                $args[] = $argument;
            }
            return $this->get($class, $args);
        }
        return null;
    }

    public function deleteDataDispatcher(string $class)
    {
        $this->classValidation($class, DataDispatcherInterface::class);
        unset($this->dataDispatcherClasses[$class::getKeyword()]);
    }
}
