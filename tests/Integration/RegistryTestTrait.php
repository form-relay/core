<?php

namespace FormRelay\Core\Tests\Integration;

use FormRelay\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\SelfContentResolver;
use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\EqualsEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\SelfEvaluation;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\SelfValueMapper;
use FormRelay\Core\CoreInitialization;
use FormRelay\Core\Factory\LoggerFactoryInterface;
use FormRelay\Core\Factory\QueueDataFactory;
use FormRelay\Core\Factory\QueueDataFactoryInterface;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\Registry;
use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Tests\Spy\DataProvider\DataProviderSpyInterface;
use FormRelay\Core\Tests\Spy\DataProvider\SpiedOnDataProvider;
use FormRelay\Core\Tests\Spy\Route\RouteSpyInterface;
use FormRelay\Core\Tests\Spy\Route\SpiedOnRoute;

trait RegistryTestTrait //  extends \PHPUnit\Framework\TestCase
{


    /** @var RequestInterface */
    protected $request;

    /** @var LoggerFactoryInterface */
    protected $loggerFactory;

    /** @var QueueInterface */
    protected $queue;

    /** @var QueueDataFactoryInterface */
    protected $queueDataFactory;

    /** @var RegistryInterface */
    protected $registry;

    protected function initRegistry()
    {
        // mock everything from the outside world
        $this->request = $this->createMock(RequestInterface::class);
        $this->loggerFactory = $this->createMock(LoggerFactoryInterface::class);
        $this->queue = $this->createMock(QueueInterface::class);

        // initialize the rest regularly
        $this->queueDataFactory = new QueueDataFactory();

        $this->registry = new Registry($this->request, $this->loggerFactory, $this->queue, $this->queueDataFactory);
    }

    protected function registerAllDefaults()
    {
        CoreInitialization::initialize($this->registry);
    }

    protected function registerBasicContentResolvers()
    {
        $this->registry->registerContentResolver(GeneralContentResolver::class);
        $this->registry->registerContentResolver(SelfContentResolver::class);
        $this->registry->registerContentResolver(MultiValueContentResolver::class);
    }

    protected function registerBasicEvaluations()
    {
        $this->registry->registerEvaluation(GeneralEvaluation::class);
        $this->registry->registerEvaluation(SelfEvaluation::class);
        $this->registry->registerEvaluation(EqualsEvaluation::class);

        // TODO GeneralEvaluation should just extend AndEvaluation instead of invoking it
        $this->registry->registerEvaluation(AndEvaluation::class);
    }

    protected function registerBasicValueMappers()
    {
        $this->registry->registerValueMapper(GeneralValueMapper::class);
        $this->registry->registerValueMapper(SelfValueMapper::class);
    }

    protected function registerRouteSpy()
    {
        $spy = $this->createMock(RouteSpyInterface::class);
        $this->registry->registerRoute(SpiedOnRoute::class, [$spy]);
        return $spy;
    }

    protected function registerDataProviderSpy()
    {
        $spy = $this->createMock(DataProviderSpyInterface::class);
        $this->registry->registerDataProvider(SpiedOnDataProvider::class, [$spy]);
        return $spy;
    }
}
