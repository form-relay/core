<?php

namespace FormRelay\Core\Tests\Integration\Service;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Model\Queue\JobInterface;
use FormRelay\Core\Queue\QueueException;
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

    /** @var Relay */
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

    public function processAddContextProvider(): array
    {
        return [
            [false, false, false, false],
            [false, false, false, true],
            [false, false, true,  false],
            [false, false, true,  true],
            [false, true,  false, false],
            [false, true,  false, true],
            [false, true,  true,  false],
            [false, true,  true,  true],

            [true,  false, false, false],
            [true,  false, false, true],
            [true,  false, true,  false],
            [true,  false, true,  true],
            [true,  true,  false, false],
            [true,  true,  false, true],
            [true,  true,  true,  false],
            [true,  true,  true,  true],
        ];
    }

    /**
     * @param bool $async
     * @param $disableStorage
     * @param $routeEnabled
     * @param $dataProviderEnabled
     * @dataProvider processAddContextProvider
     * @test
     */
    public function processAddContext(bool $async, bool $disableStorage, bool $routeEnabled, bool $dataProviderEnabled)
    {
        $this->setSubmissionAsync($async);
        $this->setStorageDisabled($disableStorage);
        $this->submissionData = ['field1' => 'value1', 'field2' => 'value2'];
        $this->addRouteSpy([
            'enabled' => $routeEnabled,
            'fields' => [ 'field1ext' => 'constValue1', ],
        ]);
        $this->addDataProviderSpy([
            'enabled' => $dataProviderEnabled,
        ]);

        // routes always add their context
        $this->routeSpy->expects($this->once())->method('addContext');

        // data providers only add their context if they are enabled
        $this->dataProviderSpy->expects($dataProviderEnabled ? $this->once() : $this->never())->method('processContext');

        $this->subject->process($this->getSubmission());
    }

    /** @test */
    public function processSyncOneRouteWithMultiplePasses()
    {
        $this->setSubmissionAsync(false);
        $this->setStorageDisabled(false);
        $this->addRouteSpy([
            'enabled' => true,
            'fields' => [ 'field1ext' => 'constValue1', ],
            'passes' => [[], []],
        ]);
        $this->submissionData = [ 'field1' => 'value1', ];
        $this->queue->expects($this->exactly(2))->method('addJob');
        $this->queue->expects($this->once())->method('markListAsRunning');
        $this->routeSpy->expects($this->exactly(2))->method('send')->with([
            'field1ext' => 'constValue1',
        ]);
        $this->queue->expects($this->exactly(2))->method('markAsDone');

        $this->subject->process($this->getSubmission());
    }

    /** @test */
    public function processAsyncOneRouteWithMultiplePasses()
    {
        $this->setSubmissionAsync(true);
        $this->setStorageDisabled(false);
        $this->addRouteSpy([
            'enabled' => true,
            'fields' => [ 'field1ext' => 'constValue1', ],
            'passes' => [[], []],
        ]);
        $this->submissionData = [ 'field1' => 'value1', ];
        $this->queue->expects($this->exactly(2))->method('addJob');
        $this->queue->expects($this->never())->method('markListAsRunning');
        $this->routeSpy->expects($this->never())->method('send');
        $this->queue->expects($this->never())->method('markAsDone');

        $this->subject->process($this->getSubmission());
    }

    /** @test */
    public function processJobThatSucceeds()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
                'field3' => [ 'type' => 'string', 'value' => 'value3' ],
            ],
            [
                'enabled' => true,
                'fields' => [
                    'field1ext' => [ 'field' => 'field1' ],
                    'field2ext' => [ 'field' => 'field2' ],
                ],
            ]
        );
        $this->routeSpy->expects($this->once())->method('send')->with([
            'field1ext' => 'value1',
            'field2ext' => 'value2',
        ]);
        $result = $this->subject->processJob($job);
        $this->assertTrue($result);
    }

    public function processJobFromSubmissionWithTwoPassesThatBothSucceedsProvider(): array
    {
        return [
            'first pass' =>  [0],
            'second pass' => [1],
        ];
    }

    /**
     * @param $pass
     * @throws QueueException
     * @dataProvider processJobFromSubmissionWithTwoPassesThatBothSucceedsProvider
     * @test
     */
    public function processJobFromSubmissionWithTwoPassesThatBothSucceed($pass)
    {
        $expectedDataPerRoutePass = [
            0 => ['field1ext' => 'value2', 'field2ext' => 'value1',],
            1 => ['field1ext' => 'value2', 'field2ext' => 'value3',],
        ];
        $this->routeSpy = $this->registerRouteSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
                'field3' => [ 'type' => 'string', 'value' => 'value3' ],
            ],
            [
                'enabled' => true,
                'fields' => [
                    'field1ext' => [ 'field' => 'field1' ],
                    'field2ext' => [ 'field' => 'field2' ],
                ],
                'passes' => [
                    [
                        'fields' => [
                            'field1ext' => [ 'field' => 'field2' ],
                            'field2ext' => [ 'field' => 'field1' ],
                        ],
                    ],
                    [
                        'fields' => [
                            'field1ext' => [ 'field' => 'field2' ],
                            'field2ext' => [ 'field' => 'field3' ],
                        ],
                    ],
                ],
            ],
            $pass
        );
        $this->routeSpy->expects($this->once())->method('send')->with($expectedDataPerRoutePass[$pass]);
        $result = $this->subject->processJob($job);
        $this->assertTrue($result);
    }

    /** @test */
    public function processJobThatSucceedsButIsSkipped()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
            ],
            [
                'enabled' => false,
                'fields' => [
                    'field1ext' => [ 'field' => 'field1' ],
                    'field2ext' => [ 'field' => 'field2' ],
                ],
            ]
        );
        $this->routeSpy->expects($this->never())->method('send');
        $result = $this->subject->processJob($job);
        $this->assertFalse($result);
    }

    /** @test */
    public function processJobThatSucceedsButIsSkippedBecauseOfItsGate()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
            ],
            [
                'enabled' => true,
                'gate' => [
                    'field1' => 'value2',
                ],
                'fields' => [
                    'field1ext' => [ 'field' => 'field1' ],
                    'field2ext' => [ 'field' => 'field2' ],
                ],
            ]
        );
        $this->routeSpy->expects($this->never())->method('send');
        $result = $this->subject->processJob($job);
        $this->assertFalse($result);
    }

    /** @test */
    public function processJobThatSucceedsAndIsNotSkippedBecauseOfAForeignGate()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
            ],
            [
                'enabled' => true,
                'gate' => [
                    'gate' => 'route2',
                ],
                'fields' => [
                    'field1ext' => [ 'field' => 'field1' ],
                    'field2ext' => [ 'field' => 'field2' ],
                ],
            ],
            0,
            [
                [
                    'routes' => [
                        'route2' => [
                            'enabled' => true,
                        ],
                    ],
                ]
            ]
        );
        $this->routeSpy->expects($this->once())->method('send')->with([
            'field1ext' => 'value1',
            'field2ext' => 'value2',
        ]);
        $result = $this->subject->processJob($job);
        $this->assertTrue($result);
    }

    /** @test */
    public function processJobThatFails()
    {
        $errorMessage = 'my error message';
        $this->routeSpy = $this->registerRouteSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
            ],
            [
                'enabled' => true,
                'fields' => [
                    'field1ext' => [ 'field' => 'field1' ],
                    'field2ext' => [ 'field' => 'field2' ],
                ],
            ]
        );
        $this->routeSpy->expects($this->once())->method('send')->willThrowException(new FormRelayException($errorMessage));
        $this->expectException(QueueException::class);
        $this->expectExceptionMessage($errorMessage);
        $this->subject->processJob($job);
    }

    /** @test */
    public function processJobWithDataProviderThatIsEnabled()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $this->dataProviderSpy = $this->registerDataProviderSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
            ],
            [
                'enabled' => true,
                'fields' => [
                    'field1ext' => 'constValue1',
                ]
            ],
            0,
            [
                [
                    'dataProviders' => [
                        'generic' => [
                            'enabled' => true,
                        ]
                    ]
                ]
            ]
        );
        $this->dataProviderSpy->expects($this->once())->method('process');
        $this->routeSpy->expects($this->once())->method('send')->with(['field1ext' => 'constValue1']);
        $result = $this->subject->processJob($job);
        $this->assertTrue($result);
    }

    /** @test */
    public function processJobWithDataProviderThatIsNotEnabled()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $this->dataProviderSpy = $this->registerDataProviderSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
            ],
            [
                'enabled' => true,
                'fields' => [ 'field1ext' => 'constValue1', ]
            ],
            0,
            [
                [
                    'dataProviders' => [
                        'generic' => [ 'enabled' => false, ]
                    ]
                ]
            ]
        );
        $this->dataProviderSpy->expects($this->never())->method('process');
        $this->routeSpy->expects($this->once())->method('send')->with(['field1ext' => 'constValue1']);
        $result = $this->subject->processJob($job);
        $this->assertTrue($result);
    }

    /** @test */
    public function processJobWithDataProviderThatIsEnabledButRouteIsDisabled()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $this->dataProviderSpy = $this->registerDataProviderSpy();
        $job = $this->createJob(
            [
                'field1' => [ 'type' => 'string', 'value' => 'value1' ],
                'field2' => [ 'type' => 'string', 'value' => 'value2' ],
            ],
            [
                'enabled' => false,
                'fields' => [ 'field1ext' => 'constValue1', ]
            ],
            0,
            [
                [
                    'dataProviders' => [
                        'generic' => [ 'enabled' => true, ]
                    ]
                ]
            ]
        );
        $this->dataProviderSpy->expects($this->once())->method('process');
        $this->routeSpy->expects($this->never())->method('send');
        $result = $this->subject->processJob($job);
        $this->assertFalse($result);
    }

    /** @test */
    public function processTwoJobsWithSameSubmissionTriggersDataProviderOnlyOnce()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $this->dataProviderSpy = $this->registerDataProviderSpy();
        $job1 = $this->createJob(
            [ 'field1' => [ 'type' => 'string', 'value' => 'value1' ] ],
            [
                'enabled' => true,
                'fields' => [ 'field1ext' => 'constValue1' ],
                'passes' => [[], []],
            ],
            0,
            [
                [
                    'dataProviders' => [
                        'generic' => [ 'enabled' => true, ],
                    ],
                ]
            ]
        );

        $job2 = $this->createJob(
            [ 'field1' => [ 'type' => 'string', 'value' => 'value1' ] ],
            [
                'enabled' => true,
                'fields' => [ 'field1ext' => 'constValue1' ],
                'passes' => [[], []],
            ],
            1,
            [
                [
                    'dataProviders' => [
                        'generic' => [ 'enabled' => true, ],
                    ],
                ]
            ]
        );

        $this->dataProviderSpy->expects($this->once())->method('process');
        $this->subject->processJob($job1);
        $this->subject->processJob($job2);
    }

    /** @test */
    public function processTwoJobsWithDifferentSubmissionsTriggersDataProviderTwice()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $this->dataProviderSpy = $this->registerDataProviderSpy();
        $job1 = $this->createJob(
            [ 'field1' => [ 'type' => 'string', 'value' => 'value1' ] ],
            [
                'enabled' => true,
                'fields' => [ 'field1ext' => 'constValue1' ],
                'passes' => [[], []],
            ],
            0,
            [
                [
                    'dataProviders' => [
                        'generic' => [ 'enabled' => true, ],
                    ],
                ]
            ]
        );

        $job2 = $this->createJob(
            [ 'field1' => [ 'type' => 'string', 'value' => 'value2' ] ],
            [
                'enabled' => true,
                'fields' => [ 'field1ext' => 'constValue1' ],
                'passes' => [[], []],
            ],
            1,
            [
                [
                    'dataProviders' => [
                        'generic' => [ 'enabled' => true, ],
                    ],
                ]
            ]
        );

        $this->dataProviderSpy->expects($this->exactly(2))->method('process');
        $this->subject->processJob($job1);
        $this->subject->processJob($job2);
    }

    /** @test */
    public function processJobWhichProducesNoDataCausesQueueException()
    {
        $this->routeSpy = $this->registerRouteSpy();
        $job = $this->createJob(
            [ 'field1' => [ 'type' => 'string', 'value' => 'value1' ], ],
            [
                'enabled' => true,
            ]
        );
        $this->expectException(QueueException::class);
        $this->expectExceptionMessage('no data generated for route "generic" in pass 0.');
        $this->subject->processJob($job);
    }
}
