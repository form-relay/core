<?php

namespace FormRelay\Core\Tests\Unit\Queue;

use FormRelay\Core\Queue\JobInterface;
use FormRelay\Core\Queue\NonPersistentQueue;
use FormRelay\Core\Queue\QueueInterface;
use PHPUnit\Framework\TestCase;

class NonPersistentQueueTest extends TestCase
{
    /** @var QueueInterface */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new NonPersistentQueue();
    }

    public function statusProvider(): array
    {
        return [
            [QueueInterface::STATUS_PENDING],
            [QueueInterface::STATUS_RUNNING],
            [QueueInterface::STATUS_DONE],
            [QueueInterface::STATUS_FAILED],
        ];
    }

    /** @test */
    public function addOneJob()
    {
        $data = ['value1'];
        /** @var JobInterface $job */
        $job = $this->subject->addJob($data);
        $this->assertInstanceOf(JobInterface::class, $job);
        $this->assertEquals($data, $job->getData());
        $this->assertEquals(QueueInterface::STATUS_PENDING, $job->getStatus());

        $jobs = $this->subject->fetch();
        $this->assertCount(1, $jobs);
        $this->assertEquals($job, $jobs[0]);
    }

    /**
     * @param $status
     * @dataProvider statusProvider
     * @test
     */
    public function addOneJobWithStatus($status)
    {
        $data = ['value1'];
        $job = $this->subject->addJob($data, $status);
        $this->assertInstanceOf(JobInterface::class, $job);
        $this->assertEquals($data, $job->getData());
        $this->assertEquals($status, $job->getStatus());

        $jobs = $this->subject->fetch();
        $this->assertCount(1, $jobs);
        $this->assertEquals($job, $jobs[0]);
    }

    /** @test */
    public function addTwoJobs()
    {
        $this->subject->addJob(['value1']);
        $this->subject->addJob(['value2']);

        $jobs = $this->subject->fetch();
        $this->assertCount(2, $jobs);
    }

    /** @test */
    public function removeJob()
    {
        $this->subject->addJob(['value1']);
        $this->subject->addJob(['value2']);

        $jobs = $this->subject->fetch();
        $this->assertCount(2, $jobs);

        $this->subject->removeJob($jobs[0]);
        $jobs = $this->subject->fetch();
        $this->assertCount(1, $jobs);

        $this->subject->removeJob($jobs[0]);
        $jobs = $this->subject->fetch();
        $this->assertCount(0, $jobs);
    }

    public function fetchProvider(): array
    {
        return [
            // limit, offset, expectedCount, statusFilter, jobStatusArray
            [0, 0, 0, [], []],

            [0, 0, 2, [], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [0, 0, 2, [], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE]],

            [0, 0, 1, [QueueInterface::STATUS_PENDING], [QueueInterface::STATUS_PENDING]],
            [0, 0, 2, [QueueInterface::STATUS_PENDING], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [0, 0, 1, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE]],
            [0, 0, 0, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_PENDING]],
            [0, 0, 0, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_RUNNING]],
            [0, 0, 0, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_FAILED]],

            [0, 0, 4, [], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE,QueueInterface::STATUS_RUNNING,QueueInterface::STATUS_FAILED]],
            [0, 0, 1, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE,QueueInterface::STATUS_RUNNING,QueueInterface::STATUS_FAILED]],

            [0, 0, 2, [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE]],
            [0, 0, 1, [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_RUNNING], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE]],

            [0, 0, 4, [], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [2, 0, 2, [], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [1, 1, 1, [], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [0, 2, 2, [], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [3, 2, 2, [], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],

            [0, 0, 3, [QueueInterface::STATUS_PENDING], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [2, 0, 2, [QueueInterface::STATUS_PENDING], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [1, 1, 1, [QueueInterface::STATUS_PENDING], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [0, 2, 1, [QueueInterface::STATUS_PENDING], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
            [3, 2, 1, [QueueInterface::STATUS_PENDING], [QueueInterface::STATUS_PENDING,QueueInterface::STATUS_DONE,QueueInterface::STATUS_PENDING,QueueInterface::STATUS_PENDING]],
        ];
    }

    /**
     * @param $limit
     * @param $offset
     * @param $expectedCount
     * @param $statusFilter
     * @param $jobStatusArray
     * @dataProvider fetchProvider
     * @test
     */
    public function fetch($limit, $offset, $expectedCount, $statusFilter, $jobStatusArray)
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->addJob(['value' . $i], $s);
        }
        $jobs = $this->subject->fetch($statusFilter, $limit, $offset);
        $this->assertCount($expectedCount, $jobs);
    }

    protected function fetchStatusProvider($s1, $s2, $s3, $s4): array
    {
        return [
            [0, 0, 0, []],

            [0, 0, 1, [$s1]],
            [0, 0, 2, [$s1,$s1]],
            [0, 0, 1, [$s1,$s2]],
            [0, 0, 0, [$s2]],
            [0, 0, 0, [$s3]],
            [0, 0, 0, [$s4]],

            [0, 0, 4, [$s1,$s1,$s1,$s1]],
            [2, 0, 2, [$s1,$s1,$s1,$s1]],
            [1, 1, 1, [$s1,$s1,$s1,$s1]],
            [0, 2, 2, [$s1,$s1,$s1,$s1]],
            [3, 2, 2, [$s1,$s1,$s1,$s1]],

            [0, 0, 3, [$s1,$s2,$s1,$s1]],
            [2, 0, 2, [$s1,$s2,$s1,$s1]],
            [1, 1, 1, [$s1,$s2,$s1,$s1]],
            [0, 2, 1, [$s1,$s2,$s1,$s1]],
            [3, 2, 1, [$s1,$s2,$s1,$s1]],
        ];
    }

    public function fetchPendingProvider(): array
    {
        return $this->fetchStatusProvider(
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_FAILED
        );
    }

    /**
     * @param $limit
     * @param $offset
     * @param $expectedCount
     * @param $jobStatusArray
     * @dataProvider fetchPendingProvider
     * @test
     */
    public function fetchPending($limit, $offset, $expectedCount, $jobStatusArray)
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->addJob(['value' . $i], $s);
        }
        $jobs = $this->subject->fetchPending($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        /** @var JobInterface $job */
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_PENDING, $job->getStatus());
        }
    }

    public function fetchDoneProvider(): array
    {
        return $this->fetchStatusProvider(
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_FAILED
        );
    }

    /**
     * @param $limit
     * @param $offset
     * @param $expectedCount
     * @param $jobStatusArray
     * @dataProvider fetchDoneProvider
     * @test
     */
    public function fetchDone($limit, $offset, $expectedCount, $jobStatusArray)
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->addJob(['value' . $i], $s);
        }
        $jobs = $this->subject->fetchDone($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        /** @var JobInterface $job */
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_DONE, $job->getStatus());
        }
    }

    public function fetchFailedProvider(): array
    {
        return $this->fetchStatusProvider(
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_DONE
        );
    }

    /**
     * @param $limit
     * @param $offset
     * @param $expectedCount
     * @param $jobStatusArray
     * @dataProvider fetchFailedProvider
     * @test
     */
    public function fetchFailed($limit, $offset, $expectedCount, $jobStatusArray)
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->addJob(['value' . $i], $s);
        }
        $jobs = $this->subject->fetchFailed($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        /** @var JobInterface $job */
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_FAILED, $job->getStatus());
        }
    }

    public function fetchRunningProvider(): array
    {
        return $this->fetchStatusProvider(
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_PENDING
        );
    }

    /**
     * @param $limit
     * @param $offset
     * @param $expectedCount
     * @param $jobStatusArray
     * @dataProvider fetchRunningProvider
     * @test
     */
    public function fetchRunning($limit, $offset, $expectedCount, $jobStatusArray)
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->addJob(['value' . $i], $s);
        }
        $jobs = $this->subject->fetchRunning($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        /** @var JobInterface $job */
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_RUNNING, $job->getStatus());
        }
    }

    public function fetchRunningWithMinTimeInSecondsSinceChangedProvider(): array
    {
        return [
            [0, QueueInterface::STATUS_RUNNING, 3600, ''],
            [0, QueueInterface::STATUS_RUNNING, 3600, '+1 hour'],
            [1, QueueInterface::STATUS_RUNNING, 3600, '-2 hours'],

            [0, QueueInterface::STATUS_PENDING, 3600, ''],
            [0, QueueInterface::STATUS_PENDING, 3600, '+1 hour'],
            [0, QueueInterface::STATUS_PENDING, 3600, '-2 hours'],
        ];
    }

    /**
     * @param $expectedCount
     * @param $status
     * @param $minAge
     * @param $modify
     * @dataProvider fetchRunningWithMinTimeInSecondsSinceChangedProvider
     * @test
     */
    public function fetchRunningWithMinTimeInSecondsSinceChanged($expectedCount, $status, $minAge, $modify)
    {
        /** @var JobInterface $job */
        $job = $this->subject->addJob(['value1'], $status);

        if ($modify) {
            $job->getChanged()->modify($modify);
        }

        $jobs = $this->subject->fetchRunning(0, 0, $minAge);
        $this->assertCount($expectedCount, $jobs);
    }

    /** @test */
    public function removeAllOldJobs()
    {
        $this->subject->addJob(['value1'], QueueInterface::STATUS_DONE);
        $this->subject->addJob(['value2'], QueueInterface::STATUS_PENDING);
        $this->subject->addJob(['value3'], QueueInterface::STATUS_DONE);
        $this->subject->addJob(['value4'], QueueInterface::STATUS_DONE);
        $this->subject->addJob(['value5'], QueueInterface::STATUS_RUNNING);
        $this->subject->addJob(['value6'], QueueInterface::STATUS_FAILED);
        $this->subject->addJob(['value7'], QueueInterface::STATUS_PENDING);

        $jobs = $this->subject->fetch();
        $this->assertCount(7, $jobs);

        $jobs[0]->getCreated()->modify('-3 hours');
        $jobs[1]->getCreated()->modify('-1 day');
        $jobs[2]->getCreated()->modify('-10 minutes');
        $jobs[3]->getCreated()->modify('-2 hours');
        $jobs[4]->getCreated()->modify('+1 day');
        $jobs[5]->getCreated()->modify('+10 minutes');
        $jobs[6]->getCreated()->modify('+2 hours');

        $this->subject->removeOldJobs(3600);

        $remainingJobs = $this->subject->fetch();
        $this->assertCount(4, $remainingJobs);
    }

    /** @test */
    public function removeOldJobsThatAreDone()
    {
        $this->subject->addJob(['value1'], QueueInterface::STATUS_DONE);
        $this->subject->addJob(['value2'], QueueInterface::STATUS_PENDING);
        $this->subject->addJob(['value3'], QueueInterface::STATUS_DONE);
        $this->subject->addJob(['value4'], QueueInterface::STATUS_DONE);
        $this->subject->addJob(['value5'], QueueInterface::STATUS_RUNNING);
        $this->subject->addJob(['value6'], QueueInterface::STATUS_FAILED);
        $this->subject->addJob(['value7'], QueueInterface::STATUS_PENDING);

        $jobs = $this->subject->fetch();
        $this->assertCount(7, $jobs);

        $jobs[0]->getCreated()->modify('-3 hours');
        $jobs[1]->getCreated()->modify('-1 day');
        $jobs[2]->getCreated()->modify('-10 minutes');
        $jobs[3]->getCreated()->modify('-2 hours');
        $jobs[4]->getCreated()->modify('+1 day');
        $jobs[5]->getCreated()->modify('+10 minutes');
        $jobs[6]->getCreated()->modify('+2 hours');

        $this->subject->removeOldJobs(3600, [QueueInterface::STATUS_DONE]);

        $remainingJobs = $this->subject->fetch();
        $this->assertCount(5, $remainingJobs);
    }

    protected function markAs($status, $otherStatus, $method, $arguments = [], $expectedStatusMessage = '')
    {
        $this->subject->addJob(['value1'], $otherStatus);
        $jobs = $this->subject->fetch();
        $this->assertCount(1, $jobs);
        /** @var JobInterface $job */
        $job = $jobs[0];
        $this->assertEquals($otherStatus, $job->getStatus());

        $this->subject->$method($job, ...$arguments);
        $this->assertEquals($status, $job->getStatus());
        $this->assertEquals($expectedStatusMessage, $job->getStatusMessage());

        $jobs = $this->subject->fetch();
        $this->assertCount(1, $jobs);
        $job = $jobs[0];
        $this->assertEquals($status, $job->getStatus());
        $this->assertEquals($expectedStatusMessage, $job->getStatusMessage());
    }

    /** @test */
    public function markAsPending()
    {
        $this->markAs(
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_RUNNING,
            'markAsPending'
        );
    }

    /** @test */
    public function markAsRunning()
    {
        $this->markAs(
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_PENDING,
            'markAsRunning'
        );
    }

    /** @test */
    public function markAsDone()
    {
        $this->markAs(
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            'markAsDone'
        );
    }

    /** @test */
    public function markAsFailed()
    {
        $this->markAs(
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_RUNNING,
            'markAsFailed'
        );
    }

    /** @test */
    public function markAsFailedWithMessage()
    {
        $errorMessage = 'my error message';
        $this->markAs(
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_RUNNING,
            'markAsFailed',
            [$errorMessage],
            $errorMessage
        );
    }

    protected function markListAs($status, $otherStatus, $thirdStatus, $method, $arguments=[], $expectedStatusMessage = '')
    {
        $this->subject->addJob(['value1'], $otherStatus);
        $this->subject->addJob(['value2'], $thirdStatus);
        $this->subject->addJob(['value3'], $otherStatus);

        $jobs = $this->subject->fetch([$otherStatus]);
        $this->assertCount(2, $jobs);

        $this->subject->$method($jobs, ...$arguments);
        /** @var JobInterface $job */
        foreach ($jobs as $job) {
            $this->assertEquals($status, $job->getStatus());
            $this->assertEquals($expectedStatusMessage, $job->getStatusMessage());
        }

        $jobs = $this->subject->fetch([$status]);
        $this->assertCount(2, $jobs);
        /** @var JobInterface $job */
        foreach ($jobs as $job) {
            $this->assertEquals($status, $job->getStatus());
            $this->assertEquals($expectedStatusMessage, $job->getStatusMessage());
        }
    }

    /** @test */
    public function markListAsRunning()
    {
        $this->markListAs(
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_FAILED,
            'markListAsRunning'
        );
    }

    /** @test */
    public function markListAsDone()
    {
        $this->markListAs(
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_FAILED,
            'markListAsDone'
        );
    }

    /** @test */
    public function markListAsFailed()
    {
        $this->markListAs(
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_DONE,
            'markListAsFailed'
        );
    }
}
