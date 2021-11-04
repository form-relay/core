<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\IfValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\OriginalValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\RawValueMapper;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

/**
 * @covers RawValueMapper
 */
class RawValueMapperTest extends AbstractValueMapperTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerValueMapper(RawValueMapper::class);
    }

    /** @test */
    public function rawMatches()
    {
        $this->fieldValue = 'value1';
        $config = [
            'raw' => [
                'value1' => 'value1b'
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function rawDoesNotMatch()
    {
        $this->fieldValue = 'value1';
        $config = [
            'raw' => [
                'value2' => 'value2b'
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function rawKeywordMatches()
    {
        $this->registerBasicEvaluations();
        $this->registerBasicContentResolvers();
        $this->registry->registerValueMapper(IfValueMapper::class);
        $this->fieldValue = 'if';
        $config = [
            'raw' => [
                'if' => 'ifb',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('ifb', $result);
    }

    /** @test */
    public function rawKeywordDoesNotMatch()
    {
        $this->registerBasicEvaluations();
        $this->registerBasicContentResolvers();
        $this->registry->registerValueMapper(IfValueMapper::class);
        $this->fieldValue = 'value1';
        $config = [
            'raw' => [
                'if' => 'ifb',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }
}
