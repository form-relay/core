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

            // TODO multi values with empty items should not be considered empty
            // [new MultiValueField(['']), false],

            [new MultiValueField([]), true],
            [new MultiValueField([0]), false],
            [new MultiValueField([1]), false],
            [new MultiValueField(['0']), false],
            [new MultiValueField(['1']), false],
            [new MultiValueField(['value1']), false],

            [new DiscreteMultiValueField(), true],
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

            // TODO how should isTrue respond to multi values?
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

            // TODO replacement for tab doesn't work currently
            //['\\t', "\t"],
            ['\\n', "\n"],

            // TODO replacement for tab doesn't work currently
            //['\\s\\t\\n\\t\\s', " \t\n\t "],
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

            // TODO plain arrays currently don't have their items trimmed
            // [[' value1 '], ',', true, ['value1']],

            [new MultiValueField(), null, null, []],
            [new MultiValueField(['value1', 'value2']), null, null, ['value1', 'value2']],
            [new DiscreteMultiValueField(), null, null, []],
            [new DiscreteMultiValueField(['value1', 'value2']), null, null, ['value1', 'value2']],

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
}
