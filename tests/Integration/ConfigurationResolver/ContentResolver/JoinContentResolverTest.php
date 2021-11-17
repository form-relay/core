<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\JoinContentResolver;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

/**
 * @covers JoinContentResolver
 */
class JoinContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function joinNull()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => null,
            'join' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function joinString()
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'join' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function joinMultiValue()
    {
        $config = [
            'multiValue' => [5, 7, 17],
            'join' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("5\n7\n17", $result);
    }

    /** @test */
    public function joinMultiValueWithGlue()
    {
        $config = [
            'multiValue' => [5, 7, 17],
            'join' => [
                'glue' => ',',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("5,7,17", $result);
    }
}
