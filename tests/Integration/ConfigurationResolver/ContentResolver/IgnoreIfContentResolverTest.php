<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\IgnoreIfContentResolver;

class IgnoreIfContentResolverTest extends IgnoreContentResolverTest
{
    const KEYWORD = 'ignoreIf';

    protected function setUp(): void
    {
        parent::setUp();
        $this->addBasicEvaluations();
        $this->addContentResolver(IgnoreIfContentResolver::class);
        $this->data['field1'] = 'value1';
    }

    public function trueFalseProvider(): array
    {
        return array_merge(
            parent::trueFalseProvider(),
            [
                [['field1' => 'value1'], true],
                [['field1' => 'value2'], false],
            ]
        );
    }
}
