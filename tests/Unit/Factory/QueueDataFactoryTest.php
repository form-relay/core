<?php

namespace FormRelay\Core\Tests\Unit\Factory;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Factory\QueueDataFactory;
use FormRelay\Core\Model\Queue\JobInterface;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Model\Submission\SubmissionContextInterface;
use FormRelay\Core\Model\Submission\SubmissionDataInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Tests\Model\Form\StringField;
use FormRelay\Core\Tests\Model\Form\InvalidField;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers QueueDataFactory
 */
class QueueDataFactoryTest extends TestCase
{
    /** @var QueueDataFactory $subject */
    protected $subject;

    /** @var MockObject */
    protected $submissionData;

    /** @var MockObject */
    protected $submissionConfiguration;

    /** @var MockObject */
    protected $submissionContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new QueueDataFactory();
    }

    protected function createSubmissionMock($data, $configuration = [], $context = [])
    {
        $this->submissionData = $this->createMock(SubmissionDataInterface::class);
        $this->submissionData->method('toArray')->willReturn($data);
        $this->submissionConfiguration = $this->createMock(SubmissionConfigurationInterface::class);
        $this->submissionConfiguration->method('toArray')->willReturn($configuration);
        $this->submissionContext = $this->createMock(SubmissionContextInterface::class);
        $this->submissionContext->method('toArray')->willReturn($context);
        $submission = $this->createMock(SubmissionInterface::class);
        $submission->method('getData')->willReturn($this->submissionData);
        $submission->method('getConfiguration')->willReturn($this->submissionConfiguration);
        $submission->method('getContext')->willReturn($this->submissionContext);
        return $submission;
    }

    /** @test */
    public function convertSubmissionWithStringFieldToJob()
    {
        $submission = $this->createSubmissionMock([
            'field1' => 'value1',
        ]);
        $this->submissionConfiguration->method('getRoutePassLabel')->willReturn('route1#6');
        $job = $this->subject->convertSubmissionToJob($submission, 'route1', 5);
        $this->assertEquals([
            'route' => 'route1',
            'pass' => 5,
            'submission' => [
                'data' => [
                    'field1' => ['type' => 'string', 'value' => 'value1'],
                ],
                'configuration' => [],
                'context' => [],
            ],
        ], $job->getData());
        $this->assertEquals('FFE59B5916E8421ABF5E340A2A94FC76', $job->getHash());
        $this->assertEquals('FFE59#route1#6', $job->getLabel());
    }

    /** @test */
    public function convertSubmissionWithComplexFieldToJob()
    {
        $submission = $this->createSubmissionMock([
            'field1' => new StringField('value1'),
        ]);
        $this->submissionConfiguration->method('getRoutePassLabel')->willReturn('route1#6');
        $job = $this->subject->convertSubmissionToJob($submission, 'route1', 5);
        $this->assertEquals([
            'route' => 'route1',
            'pass' => 5,
            'submission' => [
                'data' => [
                    'field1' => ['type' => StringField::class, 'value' => ['value1']],
                ],
                'configuration' => [],
                'context' => [],
            ],
        ], $job->getData());
        $this->assertEquals('F1A37BD81D5FEA043AAE4B806DC080D2', $job->getHash());
        $this->assertEquals('F1A37#route1#6', $job->getLabel());
    }

    /** @test */
    public function convertSubmissionWithInvalidFieldToJob()
    {
        $submission = $this->createSubmissionMock([
            'field1' => new InvalidField(),
        ]);
        $this->submissionConfiguration->method('getRoutePassLabel')->willReturn('route1#6');

        $this->expectException(InvalidArgumentException::class);
        $this->subject->convertSubmissionToJob($submission, 'route1', 5);
    }

    /** @test */
    public function convertSubmissionWithDataConfigurationAndContextToJob()
    {
        $submission = $this->createSubmissionMock(
            ['field1' => 'value1'],
            [
                [
                    'confKey1' => 'confValue1',
                    'confKey2' => [
                        'confKey2.1' => 'confValue2.1',
                        'confKey2.2' => 'confValue2.2',
                    ],
                ],
            ],
            [
                'contextKey1' => 'contextValue1',
                'contextKey2' => [
                    'contextKey2.1' => 'contextValue2.1',
                    'contextKey2.2' => 'contextValue2.2',
                ]
            ],
        );
        $this->submissionConfiguration->method('getRoutePassLabel')->willReturn('route1#6');
        $job = $this->subject->convertSubmissionToJob($submission, 'route1', 5);
        $this->assertEquals([
            'route' => 'route1',
            'pass' => 5,
            'submission' => [
                'data' => [
                    'field1' => ['type' => 'string', 'value' => 'value1'],
                ],
                'configuration' => [
                    [
                        'confKey1' => 'confValue1',
                        'confKey2' => [
                            'confKey2.1' => 'confValue2.1',
                            'confKey2.2' => 'confValue2.2',
                        ],
                    ],
                ],
                'context' => [
                    'contextKey1' => 'contextValue1',
                    'contextKey2' => [
                        'contextKey2.1' => 'contextValue2.1',
                        'contextKey2.2' => 'contextValue2.2',
                    ]
                ],
            ],
        ], $job->getData());
        $this->assertEquals('8A0D1C2BE2DC7CFD1F928CE91F558CCF', $job->getHash());
        $this->assertEquals('8A0D1#route1#6', $job->getLabel());
    }

    protected function createJobMock($submissionData, $route, $pass)
    {
        $job = $this->createMock(JobInterface::class);
        $job->method('getData')->willReturn([
            'route' => $route,
            'pass' => $pass,
            'submission' => $submissionData,
        ]);
        return $job;
    }

    /** @test */
    public function convertJobWithStringFieldToSubmission()
    {
        $job = $this->createJobMock([
            'data' => [
                'field1' => ['type' => 'string', 'value' => 'value1'],
            ],
            'configuration' => [],
            'context' => [],
        ], 'route1', 0);
        $submission = $this->subject->convertJobToSubmission($job);
        $this->assertTrue($submission->getData()->fieldExists('field1'));
        $this->assertEquals('value1', $submission->getData()['field1']);
    }

    /** @test */
    public function convertJobWithComplexFieldToSubmission()
    {
        $job = $this->createJobMock([
            'data' => [
                'field1' => ['type' => StringField::class, 'value' => ['value1']],
            ],
            'configuration' => [],
            'context' => [],
        ], 'route1', 0);
        $submission = $this->subject->convertJobToSubmission($job);
        $this->assertTrue($submission->getData()->fieldExists('field1'));
        $this->assertInstanceOf(StringField::class, $submission->getData()['field1']);
        $this->assertEquals('value1', (string)$submission->getData()['field1']);
        $this->assertEquals(['value1'], $submission->getData()['field1']->pack());
    }

    /** @test */
    public function convertJobWithInvalidFieldToSubmission()
    {
        $job = $this->createJobMock([
            'data' => [
                'field1' => ['type' => InvalidField::class, 'value' => ['value1']],
            ],
            'configuration' => [],
            'context' => [],
        ], 'route1', 0);
        $this->expectException(FormRelayException::class);
        $this->subject->convertJobToSubmission($job);
    }

    /** @test */
    public function convertJobWithUnknownFieldToSubmission()
    {
        $job = $this->createJobMock([
            'data' => [
                'field1' => ['type' => 'FormRelay\Core\Model\Field\FieldClassThatDoesNotExist', 'value' => ['value1']],
            ],
            'configuration' => [],
            'context' => [],
        ], 'route1', 0);
        $this->expectException(FormRelayException::class);
        $this->subject->convertJobToSubmission($job);
    }

    public function hashDataProvider(): array
    {
        return [
            [
                $this->createSubmissionMock(
                    ['field1' => 'value1',],
                    [
                        ['conf1' => 'confValue1',],
                        ['conf1' => 'confValue1b',],
                    ],
                    ['context1' => 'contextValue1',]
                ),
                $this->createJobMock(
                    [
                        'data' => ['field1' => ['type' => 'string', 'value' => 'value1']],
                        'configuration' => [
                            ['conf1' => 'confValue1',],
                            ['conf1' => 'confValue1b',],
                        ],
                        'context' => ['context1' => 'contextValue1',]
                    ],
                    'route1',
                    0
                ),
                '5CC87CC6BAB8DECB77382F13C97C4EB6'
            ],
        ];
    }

    /**
     * @param $submission
     * @param $job
     * @param $expectedHash
     * @dataProvider hashDataProvider
     * @test
     */
    public function getSubmissionHash($submission, $job, $expectedHash)
    {
        $hash = $this->subject->getSubmissionHash($submission);
        $this->assertEquals($expectedHash, $hash);
    }

    /**
     * @param $submission
     * @param $job
     * @param $expectedHash
     * @dataProvider hashDataProvider
     * @test
     */
    public function getJobHash($submission, $job, $expectedHash)
    {
        $hash = $this->subject->getJobHash($job);
        $this->assertEquals($expectedHash, $hash);
    }

    /**
     * @param $submission
     * @param $job
     * @param $expectedHash
     * @throws FormRelayException
     * @dataProvider hashDataProvider
     * @test
     */
    public function getSubmissionAndConvertedJobHash($submission, $job, $expectedHash)
    {
        $submissionHash = $this->subject->getSubmissionHash($submission);
        $convertedJob = $this->subject->convertSubmissionToJob($submission, 'route1', 0);
        $convertedJobHash = $this->subject->getJobHash($convertedJob);
        $convertedSubmission = $this->subject->convertJobToSubmission($convertedJob);
        $convertedSubmissionHash = $this->subject->getSubmissionHash($convertedSubmission);

        $this->assertEquals($submissionHash, $convertedJobHash);
        $this->assertEquals($convertedJobHash, $convertedSubmissionHash);
    }

    /**
     * @param $submission
     * @param $job
     * @param $expectedHash
     * @throws FormRelayException
     * @dataProvider hashDataProvider
     * @test
     */
    public function getJobAndConvertedSubmissionHash($submission, $job, $expectedHash)
    {
        $jobHash = $this->subject->getJobHash($job);
        $convertedSubmission = $this->subject->convertJobToSubmission($job);
        $convertedSubmissionHash = $this->subject->getSubmissionHash($convertedSubmission);
        $convertedJob = $this->subject->convertSubmissionToJob($convertedSubmission, 'route1', 0);
        $convertedJobHash = $this->subject->getJobHash($convertedJob);

        $this->assertEquals($jobHash, $convertedSubmissionHash);
        $this->assertEquals($convertedSubmissionHash, $convertedJobHash);
    }

    /** @test */
    public function getSubmissionLabel()
    {
        $submission = $this->createSubmissionMock([]);
        $this->submissionConfiguration->method('getRoutePassLabel')->willReturn('route1');
        $label = $this->subject->getSubmissionLabel($submission, 'route1', 0);
        $this->assertEquals('9E7B6#route1', $label);
    }

    /** @test */
    public function getJobLabel()
    {
        $job = $this->createJobMock([
            'data' => [],
            'configuration' => [[]],
            'context' => [],
        ], 'route1', 0);
        $job->method('getHash')->willReturn('ABCDE');
        $label = $this->subject->getJobLabel($job);
        $this->assertEquals('ABCDE#route1', $label);
    }

    /** @test */
    public function getJobLabelWithoutOwnHash()
    {
        $job = $this->createJobMock([
            'data' => [],
            'configuration' => [[]],
            'context' => [],
        ], 'route1', 0);
        $job->method('getHash')->willReturn('');
        $label = $this->subject->getJobLabel($job);
        $this->assertEquals('9E7B6#route1', $label);
    }

    /** @test */
    public function getJobRoute()
    {
        $job = $this->createJobMock([
            'data' => [],
            'configuration' => [[]],
            'context' => [],
        ], 'route1', 5);
        $route = $this->subject->getJobRoute($job);
        $this->assertEquals($route, 'route1');
    }

    /** @test */
    public function getJobRoutePass()
    {
        $job = $this->createJobMock([
            'data' => [],
            'configuration' => [[]],
            'context' => [],
        ], 'route1', 5);
        $pass = $this->subject->getJobRoutePass($job);
        $this->assertEquals($pass, 5);
    }

    public function getSubmissionCacheKeyProvider(): array
    {
        return [
            [
                [],
                [[]],
                [],
                'a:3:{s:4:"data";a:0:{}s:13:"configuration";a:1:{i:0;a:0:{}}s:7:"context";a:0:{}}'
            ],
            [
                [
                    'field1' => 'value1',
                    'field2' => new StringField('value2')
                ],
                [
                    [
                        'conf1' => 'confValue1',
                        'conf2' => 'confValue2',
                    ]
                ],
                [
                    'ctx1' => 'ctxValue1',
                    'ctx2' => 'ctxValue2',
                ],
                'a:3:{s:4:"data";a:2:{s:6:"field1";a:2:{s:4:"type";s:6:"string";s:5:"value";s:6:"value1";}s:6:"field2";a:2:{s:4:"type";s:43:"FormRelay\Core\Tests\Model\Form\StringField";s:5:"value";a:1:{i:0;s:6:"value2";}}}s:13:"configuration";a:1:{i:0;a:2:{s:5:"conf1";s:10:"confValue1";s:5:"conf2";s:10:"confValue2";}}s:7:"context";a:2:{s:4:"ctx1";s:9:"ctxValue1";s:4:"ctx2";s:9:"ctxValue2";}}'
            ]
        ];
    }

    /**
     * @param $data
     * @param $configuration
     * @param $context
     * @param $expectedCacheKey
     * @dataProvider getSubmissionCacheKeyProvider
     * @test
     */
    public function getSubmissionCacheKey($data, $configuration, $context, $expectedCacheKey)
    {
        $submission = $this->createSubmissionMock($data, $configuration, $context);
        $cacheKey = $this->subject->getSubmissionCacheKey($submission);
        $this->assertEquals($expectedCacheKey, $cacheKey);
    }

    /** @test */
    public function updateLegacyJobDataWithLegacyData()
    {
        $job = $this->createMock(JobInterface::class);
        $job->method('getData')->willReturn([
            'data' => ['field1' => ['type' => 'string', 'value' => 'value1']],
            'configuration' => [['conf1' => 'confValue1']],
            'context' => [
                'job' => [
                    'route' => 'route1',
                    'pass' => 5,
                ],
                'otherContext' => 'otherContextValue',
            ]
        ]);
        $job->expects($this->once())->method('setData')->with([
            'route' => 'route1',
            'pass' => 5,
            'submission' => [
                'data' => ['field1' => ['type' => 'string', 'value' => 'value1']],
                'configuration' => [['conf1' => 'confValue1']],
                'context' => [
                    'otherContext' => 'otherContextValue',
                ]
            ]
        ]);
        $this->subject->updateLegacyJobData($job);
    }

    /** @test */
    public function updateLegacyJobDataWithAlreadyUpdatedData()
    {
        $job = $this->createMock(JobInterface::class);
        $job->method('getData')->willReturn([
            'route' => 'route1',
            'pass' => 5,
            'submission' => [
                'data' => ['field1' => ['type' => 'string', 'value' => 'value1']],
                'configuration' => [['conf1' => 'confValue1']],
                'context' => [
                    'otherContext' => 'otherContextValue',
                ]
            ]
        ]);
        $job->expects($this->never())->method('setData');
        $this->subject->updateLegacyJobData($job);
    }
}
