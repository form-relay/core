<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ContentResolver\FieldContentResolver;
use FormRelay\Core\Model\Form\MultiValueField;

class FieldContentResolverTest extends AbstractContentResolverTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerContentResolver(FieldContentResolver::class);
    }

    /** @test */
    public function fieldDoesNotExist()
    {
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertNull($result);
    }

    /** @test */
    public function fieldExists()
    {
        $this->submissionData['field1'] = 'value1';
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function fieldIsEmpty()
    {
        $this->submissionData['field1'] = '';
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertNotNull($result);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function fieldHasMultiValue()
    {
        $this->submissionData['field1'] = new MultiValueField(['value1', 'value2']);
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEquals(['value1','value2'], $result);
    }

    /** @test */
    public function fieldHasEmptyMultiValue()
    {
        $this->submissionData['field1'] = new MultiValueField();
        $config = [
            'field' => 'field1',
        ];
        $result = $this->runResolverTest($config);
        $this->assertMultiValueEmpty($result);
    }
}
