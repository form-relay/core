<?php

namespace FormRelay\Core\Tests\Unit\Queue;

use Exception;
use FormRelay\Core\Model\Queue\JobInterface;
use FormRelay\Core\Queue\QueueException;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Queue\QueueProcessor;
use FormRelay\Core\Queue\QueueProcessorInterface;
use FormRelay\Core\Queue\WorkerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QueueProcessorTest extends TestCase
{
    /** @var QueueProcessorInterface */
    protected $subject;

    /** @var MockObject */
    protected $queue;

    /** @var MockObject */
    protected $worker;

    /** @var array */
    protected $jobs = [];

    /** @var int */
    protected $batchSize = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = $this->createMock(WorkerInterface::class);

        $this->subject = new QueueProcessor($this->queue, $this->worker);
    }

    public function processorMethodProvider(): array
    {
        return [
            'processBatch' => ['processBatch'],
            'processAll' => ['processAll'],
            'processJobs' => ['processJobs'],
        ];
    }

    protected function prepareQueue(string $method)
    {
        switch ($method) {
            case 'processBatch':
                $this->queue->expects($this->once())->method('fetchPending')->with($this->batchSize)->willReturn($this->jobs);
                break;
            case 'processAll':
                $this->queue->expects($this->once())->method('fetchPending')->with()->willReturn($this->jobs);
                break;
            case 'processJobs':
                $this->queue->expects($this->never())->method('fetchPending');
                break;
        }
    }

    protected function executeProcessor(string $method)
    {
        switch ($method) {
            case 'processBatch':
                $this->subject->processBatch($this->batchSize);
                break;
            case 'processAll':
                $this->subject->processAll();
                break;
            case 'processJobs':
                $this->subject->processJobs($this->jobs);
                break;
        }
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processEmpty(string $method)
    {
        $this->jobs = [];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->never())->method('markListAsRunning');
        $this->queue->expects($this->never())->method('markAsDone');
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processJobThatSucceeds(string $method)
    {
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsRunning')->with($this->jobs);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willReturn(true);
        $this->queue->expects($this->once())->method('markAsDone')->with($job, false);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processLessJobsThanRequested(string $method)
    {
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 20;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsRunning')->with($this->jobs);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willReturn(true);
        $this->queue->expects($this->once())->method('markAsDone')->with($job, false);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processTwoJobsThatSucceed(string $method)
    {
        $job1 = $this->createMock(JobInterface::class);
        $job2 = $this->createMock(JobInterface::class);

        $this->jobs = [$job1, $job2];
        $this->batchSize = 2;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsRunning')->with($this->jobs);
        $this->worker->expects($this->exactly(2))->method('processJob')
            ->withConsecutive([$job1], [$job2])
            ->willReturn(true);
        $this->queue->expects($this->exactly(2))->method('markAsDone')->withConsecutive([$job1, false], [$job2, false]);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processJobThatFails(string $method)
    {
        $errorMessage = 'my error message';
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsRunning')->with($this->jobs);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willThrowException(new QueueException($errorMessage));
        $this->queue->expects($this->once())->method('markAsFailed')->with($job, $errorMessage);
        $this->queue->expects($this->never())->method('markAsDone');

        $this->executeProcessor($method);
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processTwoJobsThatBothFail(string $method)
    {
        $errorMessage = 'my error message';
        $job1 = $this->createMock(JobInterface::class);
        $job2 = $this->createMock(JobInterface::class);

        $this->jobs = [$job1, $job2];
        $this->batchSize = 2;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsRunning')->with($this->jobs);
        $this->worker->expects($this->exactly(2))
            ->method('processJob')
            ->withConsecutive([$job1], [$job2])
            ->willThrowException(new QueueException($errorMessage));
        $this->queue->expects($this->exactly(2))
            ->method('markAsFailed')
            ->withConsecutive([$job1, $errorMessage], [$job2, $errorMessage]);
        $this->queue->expects($this->never())->method('markAsDone');

        $this->executeProcessor($method);
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processJobThrowsArbitraryException(string $method)
    {
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsRunning')->with($this->jobs);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willThrowException(new Exception('my error message'));
        $this->queue->expects($this->never())->method('markAsDone');
        $this->queue->expects($this->never())->method('markAsFailed');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('my error message');

        $this->executeProcessor($method);
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processJobThatSucceedsButWasSkipped(string $method)
    {
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsRunning')->with($this->jobs);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willReturn(false);
        $this->queue->expects($this->once())->method('markAsDone')->with($job, true);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    /**
     * @param string $method
     * @dataProvider processorMethodProvider
     * @test
     */
    public function processTwoJobsOfWhichTheFirstFails(string $method)
    {
        $errorMessage = 'my error message';
        $job1 = $this->createMock(JobInterface::class);
        $job2 = $this->createMock(JobInterface::class);

        $this->jobs = [$job1, $job2];
        $this->batchSize = 2;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsRunning')->with($this->jobs);
        $this->worker->expects($this->exactly(2))->method('processJob')
            ->withConsecutive([$job1], [$job2])
            ->willReturnCallback(function($job) use ($job1, $job2, $errorMessage) {
                if ($job === $job1) {
                    throw new QueueException($errorMessage);
                }
                return true;
            });
        $this->queue->expects($this->once())->method('markAsFailed')->with($job1, $errorMessage);
        $this->queue->expects($this->once())->method('markAsDone')->with($job2, false);

        $this->executeProcessor($method);
    }
}
