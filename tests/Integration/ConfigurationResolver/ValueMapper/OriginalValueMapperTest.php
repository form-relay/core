<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\ValueMapper\OriginalValueMapper;

/**
 * @covers OriginalValueMapper
 */
class OriginalValueMapperTest extends AbstractValueMapperTest
{
    protected function setUp(): void
    {
        parent::setUp();
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
