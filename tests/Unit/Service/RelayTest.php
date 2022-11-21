<?php

namespace FormRelay\Core\Tests\Unit\Service;

use FormRelay\Core\Factory\QueueDataFactoryInterface;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Queue\JobInterface;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Queue\QueueProcessorInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Route\RouteInterface;
use FormRelay\Core\Service\RegistryInterface;
use FormRelay\Core\Service\Relay;
use FormRelay\Core\Service\RelayInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RelayTest extends TestCase
{
    /** @var MockObject */
    protected $registry;

    /** @var MockObject */
    protected $logger;

    /** @var MockObject */
    protected $request;

    /** @var MockObject */
    protected $persistentQueue;

    /** @var MockObject */
    protected $temporaryQueue;

    /** @var MockObject */
    protected $queueDataFactory;

    /** @var MockObject */
    protected $persistentQueueProcessor;

    /** @var MockObject */
    protected $temporaryQueueProcessor;

    /** @var MockObject[] */
    protected $routes = [];

    /** @var MockObject[] */
    protected $jobs = [];

    /** @var array */
    protected $routeConfigs = [];

    /** @var MockObject */
    protected $submission;

    /** @var MockObject */
    protected $submissionConfiguration;

    /** @var RelayInterface */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->persistentQueue = $this->createMock(QueueInterface::class);
        $this->temporaryQueue = $this->createMock(QueueInterface::class);
        $this->queueDataFactory = $this->createMock(QueueDataFactoryInterface::class);
        $this->persistentQueueProcessor = $this->createMock(QueueProcessorInterface::class);
        $this->temporaryQueueProcessor = $this->createMock(QueueProcessorInterface::class);

        $this->registry = $this->createMock(RegistryInterface::class);
        $this->registry->method('getLogger')->willReturn($this->logger);
        $this->registry->method('getRequest')->willReturn($this->request);
        $this->registry->method('getQueue')->willReturn($this->persistentQueue);
        $this->registry->method('getTemporaryQueue')->willReturn($this->temporaryQueue);
        $this->registry->method('getQueueDataFactory')->willReturn($this->queueDataFactory);

        $this->registry->method('getRoutes')->willReturnCallback(function() {
            return $this->routes;
        });

        $this->subject = new Relay($this->registry);

        $this->registry->method('getQueueProcessor')->willReturnMap([
            [$this->persistentQueue, $this->subject, $this->persistentQueueProcessor],
            [$this->temporaryQueue, $this->subject, $this->temporaryQueueProcessor],
        ]);
    }

    protected function initSubmission()
    {
        $this->submissionConfiguration = $this->createMock(SubmissionConfigurationInterface::class);
        $this->submissionConfiguration->method('getWithRoutePassOverride')->willReturnCallback(function($name, $route, $pass, $default) {
            return $this->routeConfigs[$route][$pass][$name];
        });

        $this->submission = $this->createMock(SubmissionInterface::class);
        $this->submission->method('getConfiguration')->willReturn($this->submissionConfiguration);
    }

    protected function addRoute(string $keyword, array $passes)
    {
        $route = $this->createMock(RouteInterface::class);
        $this->routeConfigs[$keyword] = $passes;
        $this->routes[$keyword] = $route;
        $route->method('getPassCount')->with($this->submission)->willReturn(count($passes));

        foreach ($passes as $index => $pass) {
            $job = $this->createMock(JobInterface::class);
            $this->jobs[$keyword . ':' . $index] = $job;
        }
    }

    /** @test */
    public function processSyncOneRouteOnePassWithStorage()
    {
        $this->initSubmission();
        $this->addRoute('route1', [
            ['async' => false, 'disableStorage' => false]
        ]);

        $this->logger->expects($this->never())->method('error');

        $this->queueDataFactory
            ->expects($this->exactly(1))
            ->method('convertSubmissionToJob')
            ->withConsecutive(
                [$this->submission, 'route1', 0, QueueInterface::STATUS_RUNNING]
            )
            ->willReturnOnConsecutiveCalls(...array_values($this->jobs));

        $this->persistentQueue
            ->expects($this->exactly(1))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route1:0']]
            );

        $this->temporaryQueue
            ->expects($this->never())
            ->method('addJob');

        $this->persistentQueueProcessor
            ->expects($this->once())
            ->method('processJobs')
            ->with([
                $this->jobs['route1:0'],
            ]);

        $this->temporaryQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->subject->process($this->submission);
    }

    /** @test */
    public function processSyncOneRouteOnePassWithoutStorage()
    {
        $this->initSubmission();
        $this->addRoute('route1', [
            ['async' => false, 'disableStorage' => true]
        ]);

        $this->logger->expects($this->never())->method('error');

        $this->queueDataFactory
            ->expects($this->exactly(1))
            ->method('convertSubmissionToJob')
            ->withConsecutive(
                [$this->submission, 'route1', 0, QueueInterface::STATUS_RUNNING]
            )
            ->willReturnOnConsecutiveCalls(...array_values($this->jobs));

        $this->persistentQueue
            ->expects($this->never())
            ->method('addJob');

        $this->temporaryQueue
            ->expects($this->exactly(1))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route1:0']]
            );

        $this->persistentQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->temporaryQueueProcessor
            ->expects($this->once())
            ->method('processJobs')
            ->with([
                $this->jobs['route1:0'],
            ]);

        $this->subject->process($this->submission);
    }

    /** @test */
    public function processAsyncOneRouteOnePassWithStorage()
    {
        $this->initSubmission();
        $this->addRoute('route1', [
            ['async' => true, 'disableStorage' => false]
        ]);

        $this->logger->expects($this->never())->method('error');

        $this->queueDataFactory
            ->expects($this->exactly(1))
            ->method('convertSubmissionToJob')
            ->withConsecutive(
                [$this->submission, 'route1', 0, QueueInterface::STATUS_PENDING]
            )
            ->willReturnOnConsecutiveCalls(...array_values($this->jobs));

        $this->persistentQueue
            ->expects($this->exactly(1))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route1:0']]
            );

        $this->temporaryQueue
            ->expects($this->never())
            ->method('addJob');

        $this->persistentQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->temporaryQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->subject->process($this->submission);
    }

    /** @test */
    public function processSyncOneRouteWithMultiplePasses()
    {
        $this->initSubmission();
        $this->addRoute('route1', [
            ['async' => false, 'disableStorage' => false],
            ['async' => false, 'disableStorage' => false]
        ]);

        $this->logger->expects($this->never())->method('error');

        $this->queueDataFactory
            ->expects($this->exactly(2))
            ->method('convertSubmissionToJob')
            ->withConsecutive(
                [$this->submission, 'route1', 0, QueueInterface::STATUS_RUNNING],
                [$this->submission, 'route1', 1, QueueInterface::STATUS_RUNNING]
            )
            ->willReturnOnConsecutiveCalls(...array_values($this->jobs));

        $this->persistentQueue
            ->expects($this->exactly(2))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route1:0']],
                [$this->jobs['route1:1']]
            );

        $this->temporaryQueue
            ->expects($this->never())
            ->method('addJob');

        $this->persistentQueueProcessor
            ->expects($this->once())
            ->method('processJobs')
            ->with([
                $this->jobs['route1:0'],
                $this->jobs['route1:1'],
            ]);

        $this->temporaryQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->subject->process($this->submission);
    }

    /** @test */
    public function processAsyncOneRouteWithMultiplePasses()
    {
        $this->initSubmission();
        $this->addRoute('route1', [
            ['async' => true, 'disableStorage' => false],
            ['async' => true, 'disableStorage' => false]
        ]);

        $this->logger->expects($this->never())->method('error');

        $this->queueDataFactory
            ->expects($this->exactly(2))
            ->method('convertSubmissionToJob')
            ->withConsecutive(
                [$this->submission, 'route1', 0, QueueInterface::STATUS_PENDING],
                [$this->submission, 'route1', 1, QueueInterface::STATUS_PENDING]
            )
            ->willReturnOnConsecutiveCalls(...array_values($this->jobs));

        $this->persistentQueue
            ->expects($this->exactly(2))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route1:0']],
                [$this->jobs['route1:1']]
            );

        $this->temporaryQueue
            ->expects($this->never())
            ->method('addJob');

        $this->persistentQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->temporaryQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->subject->process($this->submission);
    }

    /** @test */
    public function processSyncAndAsyncOneRouteWithMultiplePasses()
    {
        $this->initSubmission();
        $this->addRoute('route1', [
            ['async' => false, 'disableStorage' => false],
            ['async' => true, 'disableStorage' => false]
        ]);

        $this->logger->expects($this->never())->method('error');

        $this->queueDataFactory
            ->expects($this->exactly(2))
            ->method('convertSubmissionToJob')
            ->withConsecutive(
                [$this->submission, 'route1', 0, QueueInterface::STATUS_RUNNING],
                [$this->submission, 'route1', 1, QueueInterface::STATUS_PENDING]
            )
            ->willReturnOnConsecutiveCalls(...array_values($this->jobs));

        $this->persistentQueue
            ->expects($this->exactly(2))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route1:0']],
                [$this->jobs['route1:1']]
            );

        $this->temporaryQueue
            ->expects($this->never())
            ->method('addJob');

        $this->persistentQueueProcessor
            ->expects($this->once())
            ->method('processJobs')
            ->with([
                $this->jobs['route1:0'],
            ]);

        $this->temporaryQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->subject->process($this->submission);
    }

    /** @test */
    public function processAsyncWithoutStorageLogsErrorConvertsToSync()
    {
        $this->initSubmission();
        $this->addRoute('route1', [
            ['async' => true, 'disableStorage' => true],
        ]);

        $this->logger->expects($this->once())->method('error')->with('Async submissions without storage are not possible. Using sync submission instead.');

        $this->queueDataFactory
            ->expects($this->exactly(1))
            ->method('convertSubmissionToJob')
            ->withConsecutive(
                [$this->submission, 'route1', 0, QueueInterface::STATUS_RUNNING]
            )
            ->willReturnOnConsecutiveCalls(...array_values($this->jobs));

        $this->persistentQueue
            ->expects($this->never())
            ->method('addJob');

        $this->temporaryQueue
            ->expects($this->exactly(1))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route1:0']],
            );

        $this->persistentQueueProcessor
            ->expects($this->never())
            ->method('processJobs');

        $this->temporaryQueueProcessor
            ->expects($this->once())
            ->method('processJobs')
            ->with([
                $this->jobs['route1:0'],
            ]);

        $this->subject->process($this->submission);
    }

    /** @test */
    public function processMixedSyncMixedStorageMultipleRoutesWithMultiplePasses()
    {
        $this->initSubmission();
        $this->addRoute('route1', [
            ['async' => false, 'disableStorage' => false],
            ['async' => true, 'disableStorage' => false],
        ]);
        $this->addRoute('route2', [
            ['async' => false, 'disableStorage' => true],
            ['async' => true, 'disableStorage' => true], // should be converted to be sync
        ]);

        $this->logger->expects($this->once())->method('error')->with('Async submissions without storage are not possible. Using sync submission instead.');

        $this->queueDataFactory
            ->expects($this->exactly(4))
            ->method('convertSubmissionToJob')
            ->withConsecutive(
                [$this->submission, 'route1', 0, QueueInterface::STATUS_RUNNING],
                [$this->submission, 'route1', 1, QueueInterface::STATUS_PENDING],
                [$this->submission, 'route2', 0, QueueInterface::STATUS_RUNNING],
                [$this->submission, 'route2', 1, QueueInterface::STATUS_RUNNING]
            )
            ->willReturnOnConsecutiveCalls(...array_values($this->jobs));

        $this->persistentQueue
            ->expects($this->exactly(2))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route1:0']],
                [$this->jobs['route1:1']]
            );

        $this->temporaryQueue
            ->expects($this->exactly(2))
            ->method('addJob')
            ->withConsecutive(
                [$this->jobs['route2:0']],
                [$this->jobs['route2:1']],
            );

        $this->persistentQueueProcessor
            ->expects($this->once())
            ->method('processJobs')
            ->with([
                $this->jobs['route1:0'],
            ]);

        $this->temporaryQueueProcessor
            ->expects($this->once())
            ->method('processJobs')
            ->with([
                $this->jobs['route2:0'],
                $this->jobs['route2:1'],
            ]);

        $this->subject->process($this->submission);
    }
}
