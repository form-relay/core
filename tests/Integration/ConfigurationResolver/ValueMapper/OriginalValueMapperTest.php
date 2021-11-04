<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\IfValueMapper;
use FormRelay\Core\ConfigurationResolver\ValueMapper\OriginalValueMapper;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

class OriginalValueMapperTest extends AbstractValueMapperTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerValueMapper(OriginalValueMapper::class);
        $this->setupDummyData();
    }

    /** @test */
    public function original()
    {
        $this->fieldValue = 'value1';
        $config = [
            'originalValue' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function ifOriginal()
    {
        $this->registerBasicEvaluations();
        $this->registerBasicContentResolvers();
        $this->registry->registerValueMapper(IfValueMapper::class);
        $this->fieldValue = 'value1';
        $config = [
            'if' => [
                'field2' => 'value2',
                'then' => [
                    'originalValue' => true,
                ],
                'else' => [
                    'value1' => 'value1b',
                ],
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }
}
