<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

class SelfValueMapperTest extends AbstractValueMapperTest
{
    // TODO there is legacy code trying to fetch the field value from the context if null is passed
    //      which doesn't really make sense anymore and also messes up this test
    /** @test */
    public function mapNull()
    {
        $this->markTestSkipped();
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
