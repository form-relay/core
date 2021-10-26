<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\IgnoreIfEmptyContentResolver;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class IgnoreIfEmptyContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addContentResolver(IgnoreIfEmptyContentResolver::class);
    }

    public function trueFalseProvider(): array
    {
        return [
            [true,  true],
            [false, false],
        ];
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreString($ignore, $enabled)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyString($ignore, $enabled)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => '',
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverTest($config);
        if ($enabled) {
            $this->assertNull($result);
        } else {
            $this->assertEquals('', $result);
        }
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreNull($ignore, $enabled)
    {
        $config = [
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverTest($config);
        $this->assertNull($result);
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValue($ignore, $enabled)
    {
        $config = [
            'multiValue' => [5, 7, 13],
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals([5, 7, 13], $result);
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyMultiValue($ignore, $enabled)
    {
        $config = [
            'multiValue' => [],
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverTest($config);
        if ($enabled) {
            $this->assertNull($result);
        } else {
            $this->assertMultiValueEmpty($result);
        }
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValueWithEmptyItemsOnly($ignore, $enabled)
    {
        $config = [
            'multiValue' => ['', ''],
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['', ''], $result);
    }
}
