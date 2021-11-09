<?php

namespace FormRelay\Core\Tests\Integration\Factory;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Factory\QueueDataFactory;
use FormRelay\Core\Model\File\FileInterface;
use FormRelay\Core\Model\Form\DiscreteMultiValueField;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Form\UploadField;
use FormRelay\Core\Model\Queue\Job;
use FormRelay\Core\Model\Submission\Submission;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers QueueDataFactory
 */
class QueueDataFactoryTest extends TestCase
{
    /** @var QueueDataFactory $subject */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new QueueDataFactory();
    }

    protected function routePassProvider(): array
    {
        return [
            ['route1', 0],
            ['route2', 5],
        ];
    }

    protected function packDataProvider(): array
    {
        $file = $this->createMock(FileInterface::class);
        $file->method('getName')->willReturn($arguments[0] ?? 'name1');
        $file->method('getPublicUrl')->willReturn($arguments[1] ?? 'url1');
        $file->method('getRelativePath')->willReturn($arguments[2] ?? 'path1');
        $file->method('getMimeType')->willReturn($arguments[3] ?? 'type1');
        return [
            [[], []],
            [
                [
                    'field1' => 'value1',
                    'field2' => 'value2',
                    'field3' => new MultiValueField(),
                    'field4' => new MultiValueField([5, 7, 17]),
                    'field5' => new DiscreteMultiValueField(),
                    'field6' => new DiscreteMultiValueField([5, 7, 17]),
                    'field7' => new UploadField($file),
                ],
                [
                    'field1' => ['type' => 'string', 'value' => 'value1'],
                    'field2' => ['type' => 'string', 'value' => 'value2'],
                    'field3' => ['type' => MultiValueField::class, 'value' => []],
                    'field4' => ['type' => MultiValueField::class, 'value' => [5, 7, 17]],
                    'field5' => ['type' => DiscreteMultiValueField::class, 'value' => []],
                    'field6' => ['type' => DiscreteMultiValueField::class, 'value' => [5, 7, 17]],
                    'field7' => ['type' => UploadField::class, 'value' => ['fileName' => 'name1', 'publicUrl' => 'url1', 'relativePath' => 'path1', 'mimeType' => 'type1']],
                ],
            ],
        ];
    }

    protected function packConfigurationProvider(): array
    {
        return [
            [[], []],
            [['confKey1' => 'confValue1'], ['confKey1' => 'confValue1']],
        ];
    }

    protected function packContextProvider(): array
    {
        return [
            [[], []],
            [['contextKey1' => 'contextValue1'], ['contextKey1' => 'contextValue1']],
        ];
    }


    public function packProvider(): array
    {
        $result = [];
        foreach ($this->packDataProvider() as list($data, $packedData)) {
            foreach ($this->packConfigurationProvider() as list($configuration, $packedConfiguration)) {
                foreach ($this->packContextProvider() as list($context, $packedContext)) {
                    foreach ($this->routePassProvider() as list($route, $pass)) {
                        $result[] = [
                            $data,
                            [$configuration],
                            $context,
                            $route,
                            $pass,
                            [
                                'route' => $route,
                                'pass' => $pass,
                                'submission' => [
                                    'data' => $packedData,
                                    'configuration' => [$packedConfiguration],
                                    'context' => $packedContext,
                                ],
                            ],
                        ];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @param $configuration
     * @param $context
     * @param $route
     * @param $pass
     * @param $jobData
     * @dataProvider packProvider
     * @test
     */
    public function pack($data, $configuration, $context, $route, $pass, $jobData)
    {
        $submission = new Submission($data, $configuration, $context);
        $job = $this->subject->convertSubmissionToJob($submission, $route, $pass);
        $this->assertEquals($jobData, $job->getData());
    }

    /**
     * @param $data
     * @param $configuration
     * @param $context
     * @param $route
     * @param $pass
     * @param $jobData
     * @throws FormRelayException
     * @dataProvider packProvider
     * @test
     */
    public function unpack($data, $configuration, $context, $route, $pass, $jobData)
    {
        $job = new Job();
        $job->setData($jobData);
        $submission = $this->subject->convertJobToSubmission($job);

        $this->assertEquals($data, $submission->getData()->toArray());
        $this->assertEquals($configuration, $submission->getConfiguration()->toArray());
        $this->assertEquals($context, $submission->getContext()->toArray());
    }

    /**
     * @param $data
     * @param $configuration
     * @param $context
     * @param $route
     * @param $pass
     * @param $jobData
     * @throws FormRelayException
     * @dataProvider packProvider
     * @test
     */
    public function packUnpack($data, $configuration, $context, $route, $pass, $jobData)
    {
        $submission = new Submission($data, $configuration, $context);
        $job = $this->subject->convertSubmissionToJob($submission, $route, $pass);
        $this->assertEquals($jobData, $job->getData());

        /** @var SubmissionInterface $result */
        $result = $this->subject->convertJobToSubmission($job);
        $this->assertEquals($data, $result->getData()->toArray());
        $this->assertEquals($configuration, $result->getConfiguration()->toArray());
        $this->assertEquals($context, $result->getContext()->toArray());
    }
}
