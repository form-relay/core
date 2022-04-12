<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\ValueMapper\SelfValueMapper;

/**
 * @covers SelfValueMapper
 */
class SelfValueMapperTest extends AbstractValueMapperTest
{
    /** @test */
    public function mapNull()
    {
        $this->fieldValue = null;
        $config = 'value1';
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function mapConstant()
    {
        $this->fieldValue = 'value1';
        $config = 'value1b';
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }
}
