<?php

namespace FormRelay\Core\Tests\Unit\Factory;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Factory\QueueDataFactory;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Model\Submission\SubmissionContextInterface;
use FormRelay\Core\Model\Submission\SubmissionDataInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Tests\Model\Form\StringField;
use FormRelay\Core\Tests\Model\Form\InvalidField;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QueueDataFactoryTest extends TestCase
{
    /** @var QueueDataFactory $subject */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new QueueDataFactory();
    }

    protected function createSubmissionMock($data, $configuration = [], $context = [])
    {
        $submissionData = $this->createMock(SubmissionDataInterface::class);
        $submissionData->method('toArray')->willReturn($data);
        $submissionConfiguration = $this->createMock(SubmissionConfigurationInterface::class);
        $submissionConfiguration->method('toArray')->willReturn($configuration);
        $submissionContext = $this->createMock(SubmissionContextInterface::class);
        $submissionContext->method('toArray')->willReturn($context);
        $submission = $this->createMock(SubmissionInterface::class);
        $submission->method('getData')->willReturn($submissionData);
        $submission->method('getConfiguration')->willReturn($submissionConfiguration);
        $submission->method('getContext')->willReturn($submissionContext);
        return $submission;
    }

    /** @test */
    public function packUnpack()
    {
        $data = [
            'field1' => 'value1',
            'field2' => new StringField('value2'),
        ];

        $configuration = [
            [
                'confKey1' => 'confValue1',
                'confKey2' => 'confValue2',
            ],
            [
                'confKey2' => 'confValue2b',
                'confKey3' => 'confValue3',
            ],
        ];

        $context = [
            'contextKey1' => 'contextValue1',
            'contextKey2' => [
                'contextKey2.1' => 'contextValue2.1',
                'contextKey2.2' => 'contextValue2.2',
            ],
        ];

        $submission = $this->createSubmissionMock($data, $configuration, $context);

        $packed = $this->subject->pack($submission);
        $this->assertEquals([
            'data' => [
                'field1' => ['type' => 'string', 'value' => 'value1'],
                'field2' => ['type' => StringField::class, 'value' => ['value2']],
            ],
            'configuration' => $configuration,
            'context' => $context,
        ], $packed);

        $unpacked = $this->subject->unpack($packed);
        $this->assertInstanceOf(SubmissionInterface::class, $unpacked);

        $unpackedData = $unpacked->getData();
        $this->assertInstanceOf(SubmissionDataInterface::class, $unpackedData);
        $this->assertCount(2, $unpackedData);

        $this->assertArrayHasKey('field1', $unpackedData);
        $this->assertIsString($unpackedData['field1']);
        $this->assertEquals('value1', $unpackedData['field1']);

        $this->assertArrayHasKey('field2', $unpackedData);
        $this->assertInstanceOf(StringField::class, $unpackedData['field2']);
        $this->assertEquals('value2', (string)$unpackedData['field2']);

        $unpackedConfiguration = $unpacked->getConfiguration();
        $this->assertInstanceOf(SubmissionConfigurationInterface::class, $unpackedConfiguration);
        $this->assertEquals($configuration, $unpackedConfiguration->toArray());

        $unpackedContext = $unpacked->getContext();
        $this->assertInstanceOf(SubmissionContextInterface::class, $unpackedContext);
        $this->assertEquals($context, $unpackedContext->toArray());
    }

    // TODO this error is currently not caught
    /** @test */
    public function packInvalidFieldClass()
    {
        $this->markTestSkipped();
        $data = [
            'field1' => new InvalidField(),
        ];
        $submission = $this->createSubmissionMock($data);
        $this->expectException(InvalidArgumentException::class);
        $this->subject->pack($submission);
    }

    // TODO this error is currently not caught
    /** @test */
    public function unpackUnknownFieldClass()
    {
        $this->markTestSkipped();
        $packed = [
            'data' => [
                'field1' => [
                    'type' => 'FormRelay\\Core\\Model\\Form\\FieldClassThatDoesNotExist',
                    'value' => 'value1',
                ],
            ],
            'configuration' => [],
            'context' => [],
        ];
        $this->expectException(FormRelayException::class);
        $this->subject->unpack($packed);
    }

    // TODO this error is currently not caught
    /** @test */
    public function unpackInvalidFieldClass()
    {
        $this->markTestSkipped();
        $packed = [
            'data' => [
                'field1' => [
                    'type' => InvalidField::class,
                    'value' => '',
                ],
            ],
            'configuration' => [],
            'context' => [],
        ];
        $this->expectException(FormRelayException::class);
        $this->subject->unpack($packed);
    }
}
