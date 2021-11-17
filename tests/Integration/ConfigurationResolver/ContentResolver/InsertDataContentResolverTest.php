<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\InsertDataContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

/**
 * @covers InsertDataContentResolver
 */
class InsertDataContentResolverTest extends AbstractContentResolverTest
{
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
    public function insertDataMultiValueOnly()
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
    public function insertDataContainsMultiValue()
    {
        $this->submissionData['field1'] = new MultiValueField([5, 7, 17]);
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'field1: {field1}',
            'insertData' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('field1: 5,7,17', $result);
    }
}
