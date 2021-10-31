<?php

namespace FormRelay\Core\Tests\Integration\Factory;

use FormRelay\Core\Factory\QueueDataFactory;
use FormRelay\Core\Model\File\FileInterface;
use FormRelay\Core\Model\Form\DiscreteMultiValueField;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Form\UploadField;
use FormRelay\Core\Model\Submission\Submission;
use FormRelay\Core\Model\Submission\SubmissionInterface;
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
                    $result[] = [
                        $data,
                        $configuration,
                        $context,
                        [
                            'data' => $packedData,
                            'configuration' => $packedConfiguration,
                            'context' => $packedContext,
                        ],
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @param $configuration
     * @param $context
     * @param $packed
     * @dataProvider packProvider
     * @test
     */
    public function pack($data, $configuration, $context, $packed)
    {
        $submission = new Submission($data, $configuration, $context);
        $result = $this->subject->pack($submission);
        $this->assertEquals($packed, $result);
    }

    /**
     * @param $data
     * @param $configuration
     * @param $context
     * @param $packed
     * @dataProvider packProvider
     * @test
     */
    public function unpack($data, $configuration, $context, $packed)
    {
        /** @var SubmissionInterface $result */
        $result = $this->subject->unpack($packed);
        $this->assertInstanceOf(SubmissionInterface::class, $result);
        $this->assertEquals($data, $result->getData()->toArray());
        $this->assertEquals($configuration, $result->getConfiguration()->toArray());
        $this->assertEquals($context, $result->getContext()->toArray());
    }

    /**
     * @param $data
     * @param $configuration
     * @param $context
     * @param $packed
     * @dataProvider packProvider
     * @test
     */
    public function packUnpack($data, $configuration, $context, $packed)
    {
        $submission = new Submission($data, $configuration, $context);
        $actualPacked = $this->subject->pack($submission);
        $this->assertEquals($packed, $actualPacked);

        /** @var SubmissionInterface $result */
        $result = $this->subject->unpack($actualPacked);
        $this->assertInstanceOf(SubmissionInterface::class, $result);
        $this->assertEquals($data, $result->getData()->toArray());
        $this->assertEquals($configuration, $result->getConfiguration()->toArray());
        $this->assertEquals($context, $result->getContext()->toArray());
    }
}
