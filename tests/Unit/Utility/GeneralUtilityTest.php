<?php

namespace FormRelay\Core\Tests\Unit\Utility;

use FormRelay\Core\Model\Form\DiscreteMultiValueField;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Utility\GeneralUtility;
use PHPUnit\Framework\TestCase;

class GeneralUtilityTest extends TestCase
{
    public function valueIsEmptyProvider(): array
    {
        return [
            [null, true],
            ['', true],
            [0, false],
            [1, false],
            ['0', false],
            ['1', false],
            ['value1', false],
            [new MultiValueField(['']), false],
            [new MultiValueField([]), true],
            [new MultiValueField([0]), false],
            [new MultiValueField([1]), false],
            [new MultiValueField(['0']), false],
            [new MultiValueField(['1']), false],
            [new MultiValueField(['value1']), false],
            [new DiscreteMultiValueField([]), true],
            [new DiscreteMultiValueField(['value1']), false],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider valueIsEmptyProvider
     * @test
     */
    public function valueIsEmpty($value, $expected)
    {
        $result = GeneralUtility::isEmpty($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
    public function valueIsTrueProvider(): array
    {
        return [
            [null, false],
            ['', false],
            [0, false],
            [1, true],
            ['0', false],
            ['1', true],
            ['value1', true],
            [new MultiValueField([]), false],
            [new MultiValueField([0]), true],
            [new MultiValueField([1]), true],
            [new MultiValueField([5]), true],
            [new MultiValueField(['']), true],
            [new MultiValueField(['value1']), true],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider valueIsTrueProvider
     * @test
     */
    public function valueIsTrue($value, $expected)
    {
        $result = GeneralUtility::isTrue($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function valueIsFalse($value, $notExpected)
    {
        $result = GeneralUtility::isFalse($value);
        if ($notExpected) {
            $this->assertFalse($result);
        } else {
            $this->assertTrue($result);
        }
    }

    public function parseSeparatorStringProvider(): array
    {
        return [
            ['', ""],
            [' ', ""],
            [' value1 ', "value1"],
            ['\\s', " "],
            ['\\t', "\t"],
            ['\\n', "\n"],
            ['\\s\\t\\n\\t\\s', " \t\n\t "],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider parseSeparatorStringProvider
     * @test
     */
    public function parseSeparatorString($value, $expected)
    {
        $result = GeneralUtility::parseSeparatorString($value);
        $this->assertEquals($expected, $result);
    }

    public function isListProvider(): array
    {
        return [
            [null, false],
            ['', false],
            [0, false],
            [1, false],
            ['0', false],
            ['1', false],
            ['value1', false],
            [[], true],
            [[''], true],
            [['value1'], true],
            [new MultiValueField(), true],
            [new MultiValueField(['value1']), true],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider isListProvider
     * @test
     */
    public function isList($value, $expected)
    {
        $result = GeneralUtility::isList($value);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function castValueToArrayProvider(): array
    {
        return [
            [[], null, null, []],
            [['value1'], null, null, ['value1']],
            [[' value1 '], ',', true, ['value1']],
            [new MultiValueField([]), null, null, []],
            [new MultiValueField(['value1', 'value2']), null, null, ['value1', 'value2']],
            [new MultiValueField([' value1', 'value2 ']), null, null, ['value1', 'value2']],
            [new DiscreteMultiValueField([]), null, null, []],
            [new DiscreteMultiValueField(['value1', 'value2']), null, null, ['value1', 'value2']],
            [new DiscreteMultiValueField([' value1', 'value2 ']), null, null, ['value1', 'value2']],

            ['', null, null, []],
            ['value1', null, null, ['value1']],
            ['value1,value2', null, null, ['value1', 'value2']],
            ['value1, value2', null, null, ['value1', 'value2']],
            ['value1, value2', ',', false, ['value1', ' value2']],
            ['value1;value2', ';', null, ['value1', 'value2']],
            ['value1; value2', ';', null, ['value1', 'value2']],
            ['value1; value2', ';', false, ['value1', ' value2']],
        ];
    }

    /**
     * @param $value
     * @param $token
     * @param $trim
     * @param $expected
     * @dataProvider castValueToArrayProvider
     * @test
     */
    public function castValueToArray($value, $token, $trim, $expected)
    {
        if ($token === null && $trim === null) {
            $result = GeneralUtility::castValueToArray($value);
        } elseif ($trim === null) {
            $result = GeneralUtility::castValueToArray($value, $token);
        } else {
            $result = GeneralUtility::castValueToArray($value, $token, $trim);
        }
        $this->assertEquals($expected, $result);
    }

    public function calculateHashProvider(): array
    {
        return [
            [[], false, 'undefined'],
            [[], true, 'undefined'],
            [['key1' => 'value1'], false, 'E2E517365FFE6FEDD279364E3FA74786'],
            [['key1' => 'value1'], true, 'E2E51'],
        ];
    }

    /**
     * @param $submission
     * @param $short
     * @param $expected
     * @dataProvider calculateHashProvider
     * @test
     */
    public function calculateHash($submission, $short, $expected)
    {
        $result = GeneralUtility::calculateHash($submission, $short);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param $submission
     * @param $short
     * @param $expected
     * @dataProvider calculateHashProvider
     * @test
     */
    public function calculateHashWithIgnoredConfigurationObject($submission, $short, $expected)
    {
        $submission['configuration'] = [
            'confKey1' => 'confValue1',
            'confKey2' => 'confValue2',
        ];
        $result = GeneralUtility::calculateHash($submission, $short);
        $this->assertEquals($expected, $result);
    }

    public function shortenHashProvider(): array
    {
        return [
            ['', ''],
            ['A', 'A'],
            ['ABCDE', 'ABCDE'],
            ['ABCDEF', 'ABCDE'],
            ['ABCDEFGHIJKLM', 'ABCDE'],
        ];
    }

    /**
     * @param $hash
     * @param $expected
     * @dataProvider shortenHashProvider
     * @test
     */
    public function shortenHash($hash, $expected)
    {
        $result = GeneralUtility::shortenHash($hash);
        $this->assertEquals($expected, $result);
    }
}
