<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

/**
 * @covers IgnoreIfEmptyContentResolver
 */
class IgnoreIfContentResolverTest extends IgnoreContentResolverTest
{
    const KEYWORD = 'ignoreIf';

    protected function setUp(): void
    {
        parent::setUp();
        $this->submissionData['field1'] = 'value1';
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
