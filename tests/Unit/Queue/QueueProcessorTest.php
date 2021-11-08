<?php

namespace FormRelay\Core\Tests\Unit\Queue;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = $this->createMock(WorkerInterface::class);

        $this->subject = new QueueProcessor($this->queue, $this->worker);
    }

    /** @test */
    public function processBatchEmpty()
    {
        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([]);
        $this->subject->processBatch(1);
        $this->queue->expects($this->never())->method('markListAsRunning');
        $this->queue->expects($this->never())->method('markAsDone');
        $this->queue->expects($this->never())->method('markAsFailed');
    }

    /** @test */
    public function processBatchPendingJobThatSucceeds()
    {
        $job = $this->createMock(JobInterface::class);

        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([$job]);
        $this->queue->expects($this->once())->method('markListAsRunning')->with([$job]);

        $this->worker->expects($this->once())->method('processJob')->with($job)->willReturn(true);

        $this->queue->expects($this->once())->method('markAsDone')->with($job);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->subject->processBatch(1);
    }

    /** @test */
    public function processBatchPendingJobThatFails()
    {
        $errorMessage = 'my error message';
        $job = $this->createMock(JobInterface::class);

        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([$job]);
        $this->queue->expects($this->once())->method('markListAsRunning')->with([$job]);

        $this->worker->expects($this->once())->method('processJob')->with($job)->willThrowException(new QueueException($errorMessage));

        $this->queue->expects($this->once())->method('markAsFailed')->with($job, $errorMessage);
        $this->queue->expects($this->never())->method('markAsDone');

        $this->subject->processBatch(1);
    }

    // TODO additional tests needed
}
