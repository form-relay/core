<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\DefaultContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class DefaultContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->addContentResolver(DefaultContentResolver::class);
    }

    /** @test */
    public function defaultOnly()
    {
        $config = [
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    /** @test */
    public function null()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => null,
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    /** @test */
    public function emptyString()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => '',
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    /** @test */
    public function emptyStringWhenTrimmed()
    {
        $this->addContentResolver(TrimContentResolver::class);
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => ' ',
            'trim' => true,
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    /** @test */
    public function nonEmptyString()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function multiValue()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField(['value1', 'value2']),
            'default' => 'default1',
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals(['value1', 'value2'], $result->toArray());
    }

    /** @test */
    public function emptyMultiValue()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField(),
            'default' => 'default1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('default1', $result);
    }

    // TODO GeneralUtility::isEmpty should count the items of multiValue fields
    //      instead of imploding the whole field
    /** @test */
    public function multiValueOneEmptyItem()
    {
        $this->markTestSkipped();
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField(['']),
            'default' => 'default1',
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);

        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals([''], $result->toArray());
    }

    /** @test */
    public function multiValueMultipleEmptyItems()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField(['', '']),
            'default' => 'default1',
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals(['', ''], $result->toArray());
    }

    // TODO GeneralUtility::isEmpty should count the items of multiValue fields
    //      instead of imploding the whole field
    /** @test */
    public function multiValueMultipleEmptyItemsNoGlue()
    {
        $this->markTestSkipped();
        $multiValue = new MultiValueField(['', '']);
        $multiValue->setGlue('');
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $multiValue,
            'default' => 'default1',
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals(['', ''], $result->toArray());
    }

    /** @test */
    public function defaultIsMultiValue()
    {
        $this->contentResolverClasses['multiValue'] = MultiValueContentResolver::class;
        $config = [
            'default' => [
                'multiValue' => [
                    'value1',
                    'value2',
                ]
            ]
        ];
        /** @var MultiValueField $result */
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals(['value1', 'value2'], $result->toArray());
    }
}
