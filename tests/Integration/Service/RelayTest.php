<?php

namespace FormRelay\Core\Tests\Integration\Service;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Model\Queue\JobInterface;
use FormRelay\Core\Service\Relay;
use FormRelay\Core\Service\RelayInterface;
use FormRelay\Core\Tests\Integration\RelayTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers Relay
 */
class RelayTest extends TestCase
{
    use RelayTestTrait;

    /** @var RelayInterface */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initRelay();
        $this->subject = new Relay($this->registry);
    }

    /** @test */
    public function processSyncOneRouteOnePassWithStorage() {
        $this->setSubmissionAsync(false);
        $this->setStorageDisabled(false);
        $this->addRouteSpy([
            'enabled' => true,
            'fields' => [
                'field1ext' => [ 'field' => 'field1' ],
                'field2ext' => [ 'field' => 'field2' ],
            ],
        ]);
        $this->submissionData = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        $this->queue->expects($this->once())->method('addJob');
        $this->queue->expects($this->once())->method('markListAsRunning');
        $this->routeSpy->expects($this->once())->method('send')->with([
            'field1ext' => 'value1',
            'field2ext' => 'value2',
        ]);
        $this->queue->expects($this->once())->method('markAsDone');

        $this->temporaryQueue->expects($this->never())->method('addJob');
        $this->temporaryQueue->expects($this->never())->method('markListAsRunning');
        $this->temporaryQueue->expects($this->never())->method('markAsDone');

        $this->subject->process($this->getSubmission());
    }

    /** @test */
    public function processSyncOneRouteOnePassWithoutStorage() {
        $this->setSubmissionAsync(false);
        $this->setStorageDisabled(true);
        $this->addRouteSpy([
            'enabled' => true,
            'fields' => [
                'field1ext' => [ 'field' => 'field1' ],
                'field2ext' => [ 'field' => 'field2' ],
            ],
        ]);
        $this->submissionData = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        $this->temporaryQueue->expects($this->once())->method('addJob');
        $this->temporaryQueue->expects($this->once())->method('markListAsRunning');
        $this->routeSpy->expects($this->once())->method('send')->with([
            'field1ext' => 'value1',
            'field2ext' => 'value2',
        ]);
        $this->temporaryQueue->expects($this->once())->method('markAsDone');

        $this->queue->expects($this->never())->method('addJob');
        $this->queue->expects($this->never())->method('markListAsRunning');
        $this->queue->expects($this->never())->method('markAsDone');

        $this->subject->process($this->getSubmission());
    }

    /** @test */
    public function processAsyncOneRouteOnePassWithStorage() {
        $this->setSubmissionAsync(true);
        $this->setStorageDisabled(false);
        $this->addRouteSpy([
            'enabled' => true,
            'fields' => [
                'field1ext' => [ 'field' => 'field1' ],
                'field2ext' => [ 'field' => 'field2' ],
            ],
        ]);
        $this->submissionData = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        $this->queue->expects($this->once())->method('addJob');

        $this->routeSpy->expects($this->never())->method('send');

        $this->queue->expects($this->never())->method('markListAsRunning');
        $this->queue->expects($this->never())->method('markAsDone');

        $this->temporaryQueue->expects($this->never())->method('addJob');
        $this->temporaryQueue->expects($this->never())->method('markListAsRunning');
        $this->temporaryQueue->expects($this->never())->method('markAsDone');

        $this->subject->process($this->getSubmission());
    }

    // TODO implement more tests (process and processFromQueue)
    //      - multiple passes
    //      - gate
    //        - gate referencing foreign route
    //      - data providers

//    /** @test */
//    public function processFromQueueEmpty()
//    {
//        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([]);
//        $this->subject->processFromQueue(1);
//        $this->queue->expects($this->never())->method('markAsDone');
//        $this->queue->expects($this->never())->method('markAsFailed');
//    }
//
//    /** @test */
//    public function processFromQueuePendingJobThatSucceeds()
//    {
//        $this->routeSpy = $this->registerRouteSpy();
//
//        $job = $this->createJob(
//            [
//                'field1' => ['type' => 'string', 'value' => 'value1'],
//            ],
//            [
//                'enabled' => true,
//                'fields' => [
//                    'field1ext' => ['field' => 'field1'],
//                ],
//            ],
//            0
//        );
//
//        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([$job]);
//        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
//
//        $this->routeSpy->expects($this->once())->method('send')->with([
//            'field1ext' => 'value1'
//        ]);
//
//        $this->queue->expects($this->once())->method('markAsDone')->with($job);
//        $this->queue->expects($this->never())->method('markAsFailed');
//
//        $this->subject->processFromQueue(1);
//    }
//
//    /** @test */
//    public function processFromQueuePendingJobThatFails()
//    {
//        $errorMessage = 'my error message';
//        $this->routeSpy = $this->registerRouteSpy();
//
//        $job = $this->createJob(
//            [
//                'field1' => ['type' => 'string', 'value' => 'value1'],
//            ],
//            [
//                'enabled' => true,
//                'fields' => [
//                    'field1ext' => ['field' => 'field1'],
//                ],
//            ],
//            0
//        );
//
//        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([$job]);
//        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
//
//        $this->routeSpy->expects($this->once())->method('send'
//            )->with([
//                'field1ext' => 'value1'
//            ])
//            ->willThrowException(new FormRelayException($errorMessage));
//
//        $this->queue->expects($this->once())->method('markAsFailed')->with($job, $errorMessage);
//        $this->queue->expects($this->never())->method('markAsDone');
//
//        $this->subject->processFromQueue(1);
//    }
//
//    /** @test */
//    public function processFromQueuePendingJobWithoutData()
//    {
//        $job = $this->createMock(JobInterface::class);
//        $job->expects($this->any())->method('getData')->willReturn([]);
//        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([$job]);
//        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
//        $this->queue->expects($this->once())->method('markAsFailed')->with($job, 'job data is empty');
//        $this->queue->expects($this->never())->method('markAsDone');
//        $this->subject->processFromQueue(1);
//    }
//
//    /** @test */
//    public function processFromQueuePendingJobWithoutSubmissionData()
//    {
//        $job = $this->createMock(JobInterface::class);
//        $job->expects($this->any())->method('getData')->willReturn([
//            'configuration' => [],
//            'context' => [],
//        ]);
//        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([$job]);
//        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
//        $this->queue->expects($this->once())->method('markAsFailed')->with($job, 'job has no valid submission data');
//        $this->queue->expects($this->never())->method('markAsDone');
//        $this->subject->processFromQueue(1);
//    }
//
//    /** @test */
//    public function processFromQueuePendingJobWithoutSubmissionConfiguration()
//    {
//        $job = $this->createMock(JobInterface::class);
//        $job->expects($this->any())->method('getData')->willReturn([
//            'data' => [],
//            'context' => [],
//        ]);
//        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([$job]);
//        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
//        $this->queue->expects($this->once())->method('markAsFailed')->with($job, 'job has no valid submission configuration');
//        $this->queue->expects($this->never())->method('markAsDone');
//        $this->subject->processFromQueue(1);
//    }
//
//    /** @test */
//    public function processFromQueuePendingJobWithoutSubmissionContext()
//    {
//        $job = $this->createMock(JobInterface::class);
//        $job->expects($this->any())->method('getData')->willReturn([
//            'data' => [],
//            'configuration' => [],
//        ]);
//        $this->queue->expects($this->once())->method('fetchPending')->with(1)->willReturn([$job]);
//        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
//        $this->queue->expects($this->once())->method('markAsFailed')->with($job, 'job has no valid submission context');
//        $this->queue->expects($this->never())->method('markAsDone');
//        $this->subject->processFromQueue(1);
//    }
}
