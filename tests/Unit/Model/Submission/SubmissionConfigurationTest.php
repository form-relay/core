<?php

namespace FormRelay\Core\Tests\Unit\Model\Submission;

use FormRelay\Core\Model\Submission\SubmissionConfiguration;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use PHPUnit\Framework\TestCase;

class SubmissionConfigurationTest extends TestCase
{
    /** @var SubmissionConfigurationInterface */
    protected $subject;

    public function toArrayProvider(): array
    {
        return [
            [[]],
            [['field1' => 'value1', 'field2' => 'value2',]],
        ];
    }

    /**
     * @param $values
     * @dataProvider toArrayProvider
     * @test
     */
    public function toArray($values)
    {
        $this->subject = new SubmissionConfiguration($values);
        $result = $this->subject->toArray();
        $this->assertEquals($values, $result);
    }

    /** @test */
    public function nonExistentBasicKey()
    {
        $this->subject = new SubmissionConfiguration([]);
        $this->assertNull($this->subject->get('key'));
        $this->assertEquals('default1', $this->subject->get('key', 'default1'));
    }

    /** @test */
    public function basicKeys()
    {
        $configList = [
            [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2', $this->subject->get('key2'));
    }

    /** @test */
    public function basicKeysOverride()
    {
        $configList = [
            [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
                'key4' => 'value4',
            ],
            [
                'key2' => 'value2b',
                'key3' => 'value3b',
                'key5' => 'value5b',
                'key6' => 'value6b',
            ],
            [
                'key3' => 'value3c',
                'key4' => 'value4c',
                'key6' => 'value6c',
                'key7' => 'value7c',
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $this->assertEquals('value1', $this->subject->get('key1'));
        $this->assertEquals('value2b', $this->subject->get('key2'));
        $this->assertEquals('value3c', $this->subject->get('key3'));
        $this->assertEquals('value4c', $this->subject->get('key4'));
        $this->assertEquals('value5b', $this->subject->get('key5'));
        $this->assertEquals('value6c', $this->subject->get('key6'));
        $this->assertEquals('value7c', $this->subject->get('key7'));
    }

    /** @test */
    public function basicKeyDelete()
    {
        $configList = [
            [ 'key1' => 'value1' ],
            [ 'key1' => null ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $this->assertNull($this->subject->get('key1'));
        $this->assertEquals('default1', $this->subject->get('key1', 'default1'));
    }

    /** @test */
    public function basicKeyDeletesArray()
    {
        $configList = [
            [ 'key1' => ['key1.1' => 'value1'] ],
            [ 'key1' => null ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $this->assertNull($this->subject->get('key1'));
        $this->assertEquals('default1', $this->subject->get('key1', 'default1'));
    }

    /** @test */
    public function dataProviderFound()
    {
        $conf = [
            'conf1' => 'val1',
            'conf2' => 'val2',
        ];
        $configList = [
            [
                'dataProviders' => [
                    'dataProvider1' => $conf,
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $result = $this->subject->getDataProviderConfiguration('dataProvider1');
        $this->assertEquals($conf, $result);
    }

    /** @test */
    public function dataProviderNotFound()
    {
        $conf = [
            'conf1' => 'val1',
            'conf2' => 'val2',
        ];
        $configList = [
            [
                'dataProviders' => [
                    'dataProvider1' => $conf,
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $result = $this->subject->getDataProviderConfiguration('dataProvider2');
        $this->assertEquals([], $result);
    }

    /** @test */
    public function routeFound()
    {
        $conf = [
            'conf1' => 'val1',
            'conf2' => 'val2',
        ];
        $configList = [
            [
                'routes' => [
                    'route1' => $conf,
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $result = $this->subject->getRoutePassConfiguration('route1', 0);
        $this->assertEquals($conf, $result);
    }

    /** @test */
    public function routeNotFound()
    {
        $conf = [
            'conf1' => 'val1',
            'conf2' => 'val2',
        ];
        $configList = [
            [
                'routes' => [
                    'route1' => $conf,
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $result = $this->subject->getRoutePassConfiguration('route2', 0);
        $this->assertEquals([], $result);
    }

    /** @test */
    public function routePassCountRouteWithoutPasses()
    {
        $conf = [
            'conf1' => 'val1',
            'conf2' => 'val2',
        ];
        $configList = [
            [
                'routes' => [
                    'route1' => $conf,
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $result = $this->subject->getRoutePassCount('route1');
        $this->assertEquals(1, $result);
    }

    // TODO if a route is not found, it should return 0 passes, not 1
    //      this is due to an empty array being returned
    //      which is a valid though trivial route configuration
    /** @test */
    public function routePassCountRouteNotFound()
    {
        $this->markTestSkipped();
        $conf = [
            'conf1' => 'val1',
            'conf2' => 'val2',
        ];
        $configList = [
            [
                'routes' => [
                    'route1' => $conf,
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $result = $this->subject->getRoutePassCount('route2');
        $this->assertEquals(0, $result);
    }

    /** @test */
    public function routePassConfiguration()
    {
        $configList = [
            [
                'routes' => [
                    'route1' => [
                        'key1' => 'value1',
                        'key2' => 'value2',
                        'key3' => 'value3',
                        'passes' => [
                            [
                                'key2' => 'value2b',
                                'key4' => 'value4b'
                            ],
                            [
                                'key3' => 'value3c',
                                'key4' => 'value4c',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);

        $this->assertEquals(2, $this->subject->getRoutePassCount('route1'));

        $pass1 = $this->subject->getRoutePassConfiguration('route1', 0);
        $this->assertEquals([
            'key1' => 'value1',
            'key2' => 'value2b',
            'key3' => 'value3',
            'key4' => 'value4b',
        ], $pass1);

        $pass2 = $this->subject->getRoutePassConfiguration('route1', 1);
        $this->assertEquals([
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3c',
            'key4' => 'value4c',
        ], $pass2);
    }

    /** @test */
    public function routePassConfigurationOverride()
    {
        $configList = [
            [
                'routes' => [
                    'route1' => [
                        'key1' => 'value1',
                        'key2' => 'value2',
                        'key3' => 'value3',
                        'passes' => [
                            [
                                'key2' => 'value2b',
                                'key4' => 'value4b'
                            ],
                            [
                                'key3' => 'value3c',
                                'key4' => 'value4c',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'routes' => [
                    'route1' => [
                        'key1' => 'value1.2',
                        'passes' => [
                            0 => [
                                'key2' => null,
                                'key3' => 'value3b.2',
                            ],
                            2 => [
                                'key1' => 'value1d.2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);

        $this->assertEquals(3, $this->subject->getRoutePassCount('route1'));

        $pass1 = $this->subject->getRoutePassConfiguration('route1', 0);
        $this->assertEquals([
            'key1' => 'value1.2',
            'key3' => 'value3b.2',
            'key4' => 'value4b',
        ], $pass1);

        $pass2 = $this->subject->getRoutePassConfiguration('route1', 1);
        $this->assertEquals([
            'key1' => 'value1.2',
            'key2' => 'value2',
            'key3' => 'value3c',
            'key4' => 'value4c',
        ], $pass2);

        $pass3 = $this->subject->getRoutePassConfiguration('route1', 2);
        $this->assertEquals([
            'key1' => 'value1d.2',
            'key2' => 'value2',
            'key3' => 'value3',
        ], $pass3);
    }

    /** @test */
    public function routePassConfigurationIndicesNotContinuous()
    {
        $configList = [
            [
                'routes' => [
                    'route1' => [
                        'key1' => 'value1',
                        'passes' => [
                            10 => [
                                'key1' => 'value1.1',
                            ],
                            20 => [
                                'key1' => 'value1.2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $this->assertEquals(2, $this->subject->getRoutePassCount('route1'));

        $pass1 = $this->subject->getRoutePassConfiguration('route1', 0);
        $this->assertEquals('value1.1', $pass1['key1']);

        $pass2 = $this->subject->getRoutePassConfiguration('route1', 1);
        $this->assertEquals('value1.2', $pass2['key1']);
    }

    /** @test */
    public function routePassConfigurationIndicesNotNumerical()
    {
        $configList = [
            [
                'routes' => [
                    'route1' => [
                        'key1' => 'value1',
                        'passes' => [
                            'pass1' => [
                                'key1' => 'value1.1',
                            ],
                            'pass2' => [
                                'key1' => 'value1.2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $this->assertEquals(2, $this->subject->getRoutePassCount('route1'));

        $pass1 = $this->subject->getRoutePassConfiguration('route1', 0);
        $this->assertEquals('value1.1', $pass1['key1']);

        $pass2 = $this->subject->getRoutePassConfiguration('route1', 1);
        $this->assertEquals('value1.2', $pass2['key1']);
    }

    /** @test */
    public function routePassConfigurationOrder()
    {
        // TODO the pass indices should be sorted numerical
        $this->markTestSkipped();
        $configList = [
            [
                'routes' => [
                    'route1' => [
                        'key1' => 'value1',
                        'passes' => [
                            20 => [
                                'key1' => 'value1.2',
                            ],
                            10 => [
                                'key1' => 'value1.1',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->subject = new SubmissionConfiguration($configList);
        $this->assertEquals(2, $this->subject->getRoutePassCount('route1'));

        $pass1 = $this->subject->getRoutePassConfiguration('route1', 0);
        $this->assertEquals('value1.1', $pass1['key1']);

        $pass2 = $this->subject->getRoutePassConfiguration('route1', 1);
        $this->assertEquals('value1.2', $pass2['key1']);
    }
}
