<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\SprintfContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

/**
 * @covers SprintfContentResolver
 */
class SprintfContentResolverTest extends AbstractContentResolverTest
{
    public function sprintfProvider(): array
    {
        return [
            // value, format, expected
            [null, 'format1', null],
            [null, '', null],

            ['value1', '', 'value1'],
            ['value1', 'format1', 'format1'],
            ['value1', '%s', 'value1'],
            ['value1', 'format1:%s', 'format1:value1'],

            ['1.2', '%01.2f', '1.20'],
            [1.2, '%01.2f', '1.20'],
            [34.567, '%01.2f', '34.57'],

            [new MultiValueField(['value1', 'value2']), 'format1', 'format1'],
            [new MultiValueField(['value1', 'value2']), '%s', 'value1'],
            [new MultiValueField(['value1', 'value2']), '%s:%s', 'value1:value2'],
            [new MultiValueField([1.2, 34.567]), '%01.2f - %01.2f', '1.20 - 34.57'],
        ];
    }

    /**
     * @param $value
     * @param $format
     * @param $expected
     * @dataProvider sprintfProvider
     * @test
     */
    public function sprintf($value, $format, $expected)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $value,
            'sprintf' => $format,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, $result);
    }
}
