<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use FormRelay\Core\ConfigurationResolver\ContentResolver\NegateContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class NegateContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->addContentResolver(NegateContentResolver::class);
    }

    public function provider()
    {
        return [
            // value, true, false, expected
            [null,     null,     null,     /* => */ null],
            [null,     '1',      '0',      /* => */ null],
            [null,     '0',      '1',      /* => */ null],
            [null,     'value1', null,     /* => */ null],
            [null,     null,     'value2', /* => */ null],
            [null,     'value1', 'value2', /* => */ null],

            ['',       null,     null,     /* => */ '1'],
            ['',       '1',      '0',      /* => */ '1'],
            ['',       '0',      '1',      /* => */ '0'],
            ['',       'value1', null,     /* => */ 'value1'],
            ['',       null,     'value2', /* => */ '1'],
            ['',       'value1', 'value2', /* => */ 'value1'],

            ['0',      null,     null,     /* => */ '1'],
            ['0',      '1',      '0',      /* => */ '1'],
            ['0',      '0',      '1',      /* => */ '1'],
            ['0',      'value1', null,     /* => */ 'value1'],
            ['0',      null,     'value2', /* => */ '1'],
            ['0',      'value1', 'value2', /* => */ 'value1'],

            ['1',      null,     null,     /* => */ '0'],
            ['1',      '1',      '0',      /* => */ '0'],
            ['1',      '0',      '1',      /* => */ '0'],
            ['1',      'value1', null,     /* => */ '0'],
            ['1',      null,     'value2', /* => */ 'value2'],
            ['1',      'value1', 'value2', /* => */ 'value2'],

            ['value1', null,     null,     /* => */ '0'],
            ['value1', '1',      '0',      /* => */ '0'],
            ['value1', '0',      '1',      /* => */ '1'],
            ['value1', 'value1', null,     /* => */ '0'],
            ['value1', null,     'value2', /* => */ 'value2'],
            ['value1', 'value1', 'value2', /* => */ 'value2'],

            ['value2', null,     null,     /* => */ '0'],
            ['value2', '1',      '0',      /* => */ '0'],
            ['value2', '0',      '1',      /* => */ '1'],
            ['value2', 'value1', null,     /* => */ '0'],
            ['value2', null,     'value2', /* => */ '1'],
            ['value2', 'value1', 'value2', /* => */ 'value1'],

            ['value3', null,     null,     /* => */ '0'],
            ['value3', '1',      '0',      /* => */ '0'],
            ['value3', '0',      '1',      /* => */ '1'],
            ['value3', 'value1', null,     /* => */ '0'],
            ['value3', null,     'value2', /* => */ 'value2'],
            ['value3', 'value1', 'value2', /* => */ 'value2'],
        ];
    }

    protected function runNegate($value, $true, $false, $negate, $expected, $useNullOnTrue, $useNullOnFalse)
    {
        if ($useNullOnTrue || $useNullOnFalse) {
            // TODO null values on true or false options will currently return null, they should be ignored instead
            return;
        }
        $config = [
            SubmissionConfigurationInterface::KEY_SELF => $value,
        ];
        if ($true !== null || $false !== null || $useNullOnTrue || $useNullOnFalse) {
            $config['negate'] = [
                SubmissionConfigurationInterface::KEY_SELF => $negate,
            ];
        } else {
            $config['negate'] = $negate;
        }
        if ($true !== null || $useNullOnTrue) {
            $config['negate']['true'] = $true;
        }
        if ($false !== null || $useNullOnFalse) {
            $config['negate']['false'] = $false;
        }
        $result = $this->runResolverTest($config);
        if ($expected === null) {
            $this->assertNull($result);
        } else {
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @param $value
     * @param $true
     * @param $false
     * @param $expected
     * @dataProvider provider
     * @test
     */
    public function negateEnabled($value, $true, $false, $expected)
    {
        $this->runNegate($value, $true, $false, true, $expected, false, false);
        if ($true === null) {
            $this->runNegate($value, $true, $false, true, $expected, true, false);
        }
        if ($false === null) {
            $this->runNegate($value, $true, $false, true, $expected, false, true);
        }
        if ($false === null && $true === null) {
            $this->runNegate($value, $true, $false, true, $expected, true, true);
        }
    }

    // TODO disabling negate modifier is not implemented yet
    /**
     * @param $value
     * @param $true
     * @param $false
     * @param $expected
     * @dataProvider provider
     * @test
     */
    public function negateDisabled($value, $true, $false, $expected)
    {
        $this->markTestSkipped();
        $this->runNegate($value, $true, $false, false, $value, false, false);
        if ($true === null) {
            $this->runNegate($value, $true, $false, false, $value, true, false);
        }
        if ($false === null) {
            $this->runNegate($value, $true, $false, false, $value, false, true);
        }
        if ($false === null && $true === null) {
            $this->runNegate($value, $true, $false, false, $value, true, true);
        }
    }

    protected function runNegateMultiValue($value, $true, $false, $negate, $expected, $useNullOnTrue, $useNullOnFalse)
    {
        if ($useNullOnTrue || $useNullOnFalse) {
            // TODO null values on true or false options will currently return null, they should be ignored instead
            return;
        }
        if ($value === null) {
            $value = [];
        }
        if ($expected === null) {
            $expected = [];
        }
        $config = [
            'list' => [$value],
        ];
        if ($true !== null || $false !== null || $useNullOnTrue || $useNullOnFalse) {
            $config['negate'] = [
                SubmissionConfigurationInterface::KEY_SELF => $negate,
            ];
        } else {
            $config['negate'] = $negate;
        }
        if ($true !== null || $useNullOnTrue) {
            $config['negate']['true'] = $true;
        }
        if ($false !== null || $useNullOnFalse) {
            $config['negate']['false'] = $false;
        }
        $result = $this->runResolverTest($config);
        if (empty($expected)) {
            $this->assertMultiValueEmpty($result);
        } else {
            $this->assertMultiValueEquals($expected, $result);
        }
    }

    // TODO negate modifier does not take multi values into account
    /**
     * @param $value
     * @param $true
     * @param $false
     * @param $expected
     * @dataProvider provider
     * @test
     */
    public function negateMultiValueEnabled($value, $true, $false, $expected)
    {
        $this->markTestSkipped();
        $this->addContentResolver(ListContentResolver::class);
        $this->runNegateMultiValue($value, $true, $false, true, $expected, false, false);
        if ($true === null) {
            $this->runNegateMultiValue($value, $true, $false, true, $expected, true, false);
        }
        if ($false === null) {
            $this->runNegateMultiValue($value, $true, $false, true, $expected, false, true);
        }
        if ($false === null && $true === null) {
            $this->runNegateMultiValue($value, $true, $false, true, $expected, true, true);
        }
    }

    // TODO negate modifier does not take multi values into account
    // TODO disabling negate modifier is not implemented yet
    /**
     * @param $value
     * @param $true
     * @param $false
     * @param $expected
     * @dataProvider provider
     * @test
     */
    public function negateMultiValueDisabled($value, $true, $false, $expected)
    {
        $this->markTestSkipped();
        $this->addContentResolver(ListContentResolver::class);
        $this->runNegateMultiValue($value, $true, $false, false, $value, false, false);
        if ($true === null) {
            $this->runNegateMultiValue($value, $true, $false, false, $value, true, false);
        }
        if ($false === null) {
            $this->runNegateMultiValue($value, $true, $false, false, $value, false, true);
        }
        if ($false === null && $true === null) {
            $this->runNegateMultiValue($value, $true, $false, false, $value, true, true);
        }
    }
}
