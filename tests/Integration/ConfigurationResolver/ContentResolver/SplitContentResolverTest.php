<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\SplitContentResolver;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class SplitContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(SplitContentResolver::class);
    }

    public function splitByIndexProvider(): array
    {
        return [
            // value, index, token, expected
            [null,                 null, null, null],
            ['',                   null, null, ''],
            ['first second third', null, null, 'first'],
            ['first second third', '1',  null, 'first'],
            ['first second third', '2',  null, 'second'],
            ['first second third', '4',  null, ''],
            ['first second third', '-1', null, 'third'],
            ['first second third', '-3', null, 'first'],
            ['first second third', '-4', null, 'first'],
            ['first-second-third', '2',  '-',  'second'],
            ['first second third', '1',  '-',  'first second third'],
            ['first second third', '2',  '-',  ''],
        ];
    }

    public function splitBySpliceProvider(): array
    {
        return [
            // value, splice, token, expected
            [null,                 '1:',   null, null],
            ['',                   '1:',   null, ''],
            ['first second third', '1:',   null, 'first second third'],
            ['first second third', '2:',   null, 'second third'],
            ['first second third', '3:',   null, 'third'],
            ['first second third', '4:',   null, ''],
            ['first second third', '-1:',  null, 'third'],
            ['first second third', '-2:',  null, 'second third'],
            ['first second third', '-3:',  null, 'first second third'],
            ['first second third', '-4:',  null, 'first second third'],
            ['first second third', '1:1',  null, 'first'],
            ['first second third', '1:2',  null, 'first second'],
            ['first second third', '2:1',  null, 'second'],
            ['first second third', '2:2',  null, 'second third'],
            ['first second third', '1:-1', null, 'first second'],
            ['first second third', '1:-2', null, 'first'],
            ['first second third', ':1',   null, 'first'],
            ['first second third', ':2',   null, 'first second'],
            ['first second third', ':4',   null, 'first second third'],
            ['first second third', ':-1',  null, 'first second'],
            ['first second third', ':-2',  null, 'first'],
        ];
    }

    protected function runSplit($value, $index, $token, $expected, $direct, $pointer)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $value,
        ];
        if ($direct) {
            $config['split'] = $index !== null ? $index : true;
        } else {
            $config['split'] = [];
            if ($index !== null) {
                $config['split'][$pointer] = $index;
            }
            if ($token !== null) {
                $config['split']['token'] = $token;
            }
        }
        $result = $this->runResolverProcess($config);
        $this->assertEquals($expected, $result);
    }


    /**
     * @param $value
     * @param $index
     * @param $token
     * @param $expected
     * @dataProvider splitBySpliceProvider
     * @test
     */
    public function splitByIndex($value, $index, $token, $expected)
    {
        $this->runSplit($value, $index, $token, $expected, false, 'index');
        if ($token === null) {
            $this->runSplit($value, $index, $token, $expected, true, 'index');
        }
    }


    /**
     * @param $value
     * @param $splice
     * @param $token
     * @param $expected
     * @dataProvider splitByIndexProvider
     * @test
     */
    public function splitBySplice($value, $splice, $token, $expected)
    {
        $this->runSplit($value, $splice, $token, $expected, false, 'slice');
        if ($token === null) {
            $this->runSplit($value, $splice, $token, $expected, true, 'slice');
        }
    }
}
