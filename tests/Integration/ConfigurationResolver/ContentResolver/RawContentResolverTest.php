<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\RawContentResolver;

// NOTE: to be honest, I am not sure what this resolver was for
//       it does make sense on evaluations
//       but not really on content resolvers

class RawContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addContentResolver(RawContentResolver::class);
        $this->addContentResolver(FieldContentResolver::class);
    }

    /** @test */
    public function raw()
    {
        $config = [
            'raw' => 'field',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('field', $result);
    }
}
