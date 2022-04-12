<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\ValueMapper\SwitchValueMapper;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

/**
 * @covers SwitchValueMapper
 */
class SwitchValueMapperTest extends AbstractValueMapperTest
{
    /** @test */
    public function switchCaseMatches()
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                1 => [
                    'case' => 'value1',
                    'value' => 'value1b'
                ],
                2 => [
                    'case' => 'value2',
                    'value' => 'value2b'
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function switchCaseDoesNotMatch()
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                1 => [
                    'case' => 'value2',
                    'value' => 'value2b'
                ],
                2 => [
                    'case' => 'value3',
                    'value' => 'value3b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function switchSelfMatches()
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                1 => [
                    SubmissionConfigurationInterface::KEY_SELF => 'value1',
                    'value' => 'value1b'
                ],
                2 => [
                    'case' => 'value2',
                    'value' => 'value2b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function switchSelfDoesNotMatch()
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                1 => [
                    SubmissionConfigurationInterface::KEY_SELF => 'value2',
                    'value' => 'value2b'
                ],
                2 => [
                    'case' => 'value3',
                    'value' => 'value3b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function switchCaseMatchesKeyword()
    {
        $this->fieldValue = 'if';
        $config = [
            'switch' => [
                1 => [
                    'case' => 'if',
                    'value' => 'ifb',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('ifb', $result);
    }

    /** @test */
    public function switchUnsorted()
    {
        $this->fieldValue = 'value1';
        $config = [
            'switch' => [
                2 => [
                    'case' => 'value1',
                    'value' => 'value1c',
                ],
                1 => [
                    'case' => 'value1',
                    'value' => 'value1b',
                ]
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function switchCaseIsNumberAndWillBeCastToString()
    {
        $this->fieldValue = '12';
        $config = [
            'switch' => [
                1 => [
                    'case' => 12,
                    'value' => 'twelve',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('twelve', $result);
    }
}
