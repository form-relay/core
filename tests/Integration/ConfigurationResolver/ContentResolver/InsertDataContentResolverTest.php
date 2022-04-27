<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\InsertDataContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Tests\Model\Form\StringField;

/**
 * @covers InsertDataContentResolver
 */
class InsertDataContentResolverTest extends AbstractContentResolverTest
{
    // TODO use AbstractModifierContentResolverTest as parent
    //      though not all tests can be put into provider arrays
    //      because the content resolver can collapse multi-values to strings

    public function insertDataProvider(): array
    {
        return [
            ['{field1}, {field2}, {field3}', 'value1, value2, value3'],
            ['field1',                       'field1'],
            ['{value1},value2',              ',value2'],
            ['{field9}',                     null],
            ['\\s',                          ' '],
            ['\\t',                          "\t"],
            ['\\n',                          "\n"],
            ['field1\\s=\\s{field1}\\n{field2}\\n{field9}', "field1 = value1\nvalue2\n"],
        ];
    }

    protected function runInsertData($template, $expected, $enabled)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $template,
            'insertData' => $enabled
        ];
        $result = $this->runResolverProcess($config);
        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @param $template
     * @param $expected
     * @dataProvider insertDataProvider
     * @test
     */
    public function insertData($template, $expected)
    {
        $this->setupDummyData(3);
        $this->runInsertData($template, $expected, true);
        $this->runInsertData($template, $template, false);
    }

    /** @test */
    public function insertMultiValueIntoOnePlaceholderOnly()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => '{field1}',
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([5, 7, 17], $result);
    }

    /** @test */
    public function insertMultiValueIntoPlaceholderWithContext()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'field1: {field1}',
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('field1: 5,7,17', $result);
    }

    /** @test */
    public function insertDataIntoMultiValue()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField(['{field1}']),
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(new MultiValueField(['value1']), $result);
    }

    /** @test */
    public function insertDataIntoNestedMultiValue()
    {
        $this->submissionData['field1'] = 'value1';
        $this->submissionData['field2'] = 'value2';
        $this->submissionData['field3'] = 'value3';
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField([
                '{field1}',
                new MultiValueField(['{field2}','{field3}']),
            ]),
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(new MultiValueField([
            'value1',
            new MultiValueField(['value2','value3']),
        ]), $result);
    }

    /** @test */
    public function insertNonPrimitiveField()
    {
        $this->submissionData['field1'] = new StringField('value1');
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => '{field1}',
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertIsObject($result);
        $this->assertInstanceOf(StringField::class, $result);
        $this->assertEquals('value1', (string)$result);
    }

    /** @test */
    public function insertNonPrimitiveFieldWithContext()
    {
        $this->submissionData['field1'] = new StringField('value1');
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'field1:{field1}',
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertIsString($result);
        $this->assertEquals('field1:value1', $result);
    }

    /** @test */
    public function insertNonPrimitiveFieldIntoMultiValue()
    {
        $this->submissionData['field1'] = new StringField('value1');
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField(['{field1}']),
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(new MultiValueField(['value1']), $result);
        $this->assertIsObject($result['0']);
        $this->assertInstanceOf(StringField::class, $result['0']);
        $this->assertEquals('value1', (string)$result['0']);
    }

    /** @test */
    public function insertNonPrimitiveFieldIntoMultiValueWithContext()
    {
        $this->submissionData['field1'] = new StringField('value1');
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField(['field1:{field1}']),
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(new MultiValueField(['field1:value1']), $result);
        $this->assertIsString($result['0']);
        $this->assertEquals('field1:value1', $result['0']);
    }
}
