<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\TrimContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class TrimContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addContentResolver(TrimContentResolver::class);
    }

    public function provider()
    {
        return [
            [null,          null],
            ["",            ""],
            [" ",           ""],
            ["\t",          ""],
            ["\n",          ""],
            [" value1 ",    "value1"],
            ["val ue1",     "val ue1"],
            [" val ue1 ",   "val ue1"],
            ["value1",      "value1"],
            ["\t value1\n", "value1"],
        ];
    }

    protected function runTrim($value, $trim, $expected)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $value,
            'trim' => $trim,
        ];
        $result = $this->runResolverTest($config);
        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider provider
     * @test
     */
    public function trim($value, $expected)
    {
        $this->runTrim($value, true, $expected);
        $this->runTrim($value, false, $value);
    }

    // TODO modifiers should take multiValue fields into account
    /** @test */
    public function trimMultiValue()
    {
        $this->markTestSkipped();
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField([
                '',
                ' ',
                ' value3 ',
                'value4',
            ]),
            'trim' => true,
        ];
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $this->assertEquals(['', '', 'value3', 'value4'], $result->toArray());
    }

    // TODO modifiers should take multiValue fields into account
    /** @test */
    public function trimNestedMultiValue()
    {
        $this->markTestSkipped();
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => new MultiValueField([
                new MultiValueField([
                    '',
                    ' ',
                    ' value3 ',
                    'value4',
                ]),
            ]),
            'trim' => true,
        ];
        $result = $this->runResolverTest($config);
        $this->assertInstanceOf(MultiValueField::class, $result);
        $result = $result->toArray();
        $this->assertInstanceOf(MultiValueField::class, $result[0]);

        $result = $result[0]->toArray();
        $this->assertEquals('', $result[0]);
        $this->assertEquals('', $result[1]);
        $this->assertEquals('value3', $result[2]);
        $this->assertEquals('value4', $result[3]);
    }
}
