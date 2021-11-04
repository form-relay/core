<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\IfValueMapper;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

class IfValueMapperTest extends AbstractValueMapperTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registerBasicEvaluations();
        $this->registerBasicContentResolvers();
        $this->registry->registerValueMapper(IfValueMapper::class);
    }

    /** @test */
    public function valueIfThenExists()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'value1' => [
                'if' => [
                    'field2' => 'value2',
                    'then' => 'value1b',
                    'else' => 'value1c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function valueIfThenDoesNotExist()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'value1' => [
                'if' => [
                    'field2' => 'value2',
                    'else' => 'value1c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function valueIfElseExists()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'value1' => [
                'if' => [
                    'field2' => 'value3',
                    'then' => 'value1b',
                    'else' => 'value1c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1c', $result);
    }

    /** @test */
    public function valueIfElseDoesNotExist()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'value1' => [
                'if' => [
                    'field2' => 'value3',
                    'then' => 'value1b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function constIfThenExists()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value2',
                'then' => 'value1b',
                'else' => 'value1c',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function constIfThenDoesNotExist()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value2',
                'else' => 'value1c',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function constIfElseExists()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value3',
                'then' => 'value1b',
                'else' => 'value1c',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1c', $result);
    }

    /** @test */
    public function constIfElseDoesNotExist()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value3',
                'then' => 'value1b',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function ifValueThen()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value2',
                'then' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
                'else' => [
                    'value1' => 'value1c',
                    'value2' => 'value2c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function ifValueElse()
    {
        $this->setupDummyData();
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value3',
                'then' => [
                    'value1' => 'value1b',
                    'value2' => 'value2b',
                ],
                'else' => [
                    'value1' => 'value1c',
                    'value2' => 'value2c',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1c', $result);
    }
}
