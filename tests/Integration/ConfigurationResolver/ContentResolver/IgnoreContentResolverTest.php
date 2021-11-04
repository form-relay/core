<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\IgnoreContentResolver;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class IgnoreContentResolverTest extends AbstractContentResolverTest
{
    const KEYWORD = 'ignore';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(IgnoreContentResolver::class);
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
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreString($ignore, $ignored)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => 'value1',
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($ignored) {
            $this->assertNull($result);
        } else {
            $this->assertEquals('value1', $result);
        }
    }

    /**
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyString($ignore, $ignored)
    {
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => '',
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($ignored) {
            $this->assertNull($result);
        } else {
            $this->assertEquals('', $result);
        }
    }

    /**
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreNull($ignore, $ignored)
    {
        $config = [
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /**
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValue($ignore, $ignored)
    {
        $config = [
            'multiValue' => [5, 7, 13],
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($ignored) {
            $this->assertNull($result);
        } else {
            $this->assertMultiValueEquals([5, 7, 13], $result);
        }
    }

    /**
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyMultiValue($ignore, $ignored)
    {
        $config = [
            'multiValue' => [],
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($ignored) {
            $this->assertNull($result);
        } else {
            $this->assertMultiValueEmpty($result);
        }
    }
}
