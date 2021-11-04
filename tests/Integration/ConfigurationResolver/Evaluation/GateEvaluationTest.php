<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\AndEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\GateEvaluation;
use FormRelay\Core\ConfigurationResolver\Evaluation\OrEvaluation;

class GateEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registry->registerEvaluation(GateEvaluation::class);
        $this->registry->registerEvaluation(AndEvaluation::class);
        $this->registry->registerEvaluation(OrEvaluation::class);
        $this->setupDummyData();
        $this->createRouteConfig('routeGateSucceeds', true);
        $this->createRouteConfig('routeGateSucceeds2', true);
        $this->createRouteConfig('routeGateDoesNotSucceed', false);
        $this->createRouteConfig('routeGateDoesNotSucceed2', false);
        $this->createRouteConfig('routeAllPassesSucceed', [true, true]);
        $this->createRouteConfig('routeNoPassesSucceed', [false, false]);
        $this->createRouteConfig('routeSomePassesSucceed', [true, false]);
        $this->createRouteConfig('routeSomePassesSucceed2', [false, true]);
    }

    protected function createGateConfig($passes)
    {
        return [
            'field1' => $passes ? 'value1' : 'value2',
        ];
    }

    protected function createRouteConfig($name, $gatePasses)
    {
        $routeConf = [
            'routes' => [
                $name => [
                    'enabled' => true,
                ]
            ],
        ];
        if (is_array($gatePasses)) {
            $routeConf['routes'][$name]['passes'] = [];
            foreach ($gatePasses as $pass => $passGatePasses) {
                $routeConf['routes'][$name]['passes'][$pass] = [
                    'gate' => $this->createGateConfig($passGatePasses),
                ];
            }
        } else {
            $routeConf['routes'][$name]['gate'] = $this->createGateConfig($gatePasses);
        }
        $this->submissionConfiguration[] = $routeConf;
    }

    public function gateProvider(): array
    {
        return [
            // routeName, routePass, expected

            // routes without passes
            ['routeGateSucceeds',                                null, true],
            ['routeGateSucceeds',                               '0',   true],
            ['routeGateSucceeds',                               'any', true],
            ['routeGateSucceeds',                               'all', true],
            ['routeGateDoesNotSucceed',                          null, false],
            ['routeGateDoesNotSucceed',                         '0',   false],
            ['routeGateDoesNotSucceed',                         'any', false],
            ['routeGateDoesNotSucceed',                         'all', false],

            ['routeGateSucceeds,routeGateSucceeds2',             null, true],
            ['routeGateSucceeds,routeGateDoesNotSucceed',        null, true],
            ['routeGateDoesNotSucceed,routeGateDoesNotSucceed2', null, false],

            // routes with passes
            ['routeAllPassesSucceed',                           null,  true],
            ['routeAllPassesSucceed',                           '0',   true],
            ['routeAllPassesSucceed',                           '1',   true],
            ['routeAllPassesSucceed',                           'any', true],
            ['routeAllPassesSucceed',                           'all', true],

            ['routeNoPassesSucceed',                            null,  false],
            ['routeNoPassesSucceed',                            '0',   false],
            ['routeNoPassesSucceed',                            '1',   false],
            ['routeNoPassesSucceed',                            'any', false],
            ['routeNoPassesSucceed',                            'all', false],

            ['routeSomePassesSucceed',                          null,  true],
            ['routeSomePassesSucceed',                          '0',   true],
            ['routeSomePassesSucceed',                          '1',   false],
            ['routeSomePassesSucceed',                          'any', true],
            ['routeSomePassesSucceed',                          'all', false],

            ['routeSomePassesSucceed2',                         null,  true],
            ['routeSomePassesSucceed2',                         '1',   true],
            ['routeSomePassesSucceed2',                         '0',   false],
            ['routeSomePassesSucceed2',                         'any', true],
            ['routeSomePassesSucceed2',                         'all', false],

            ['routeAllPassesSucceed,routeNoPassesSucceed',      null,  true],
            ['routeAllPassesSucceed,routeSomePassesSucceed',    null,  true],
            ['routeNoPassesSucceed,routeSomePassesSucceed',     null,  true],
            ['routeAllPassesSucceed,routeSomePassesSucceed,routeNoPassesSucceed', null,  true],

            // mixed routes with and without passes
            ['routeGateSucceeds,routeAllPassesSucceed',         null,  true],
            ['routeGateSucceeds,routeSomePassesSucceed',        null,  true],
            ['routeGateSucceeds,routeNoPassesSucceed',          null,  true],

            ['routeGateDoesNotSucceed,routeAllPassesSucceed',   null,  true],
            ['routeGateDoesNotSucceed,routeSomePassesSucceed',  null,  true],
            ['routeGateDoesNotSucceed,routeNoPassesSucceed',    null,  false],
        ];
    }

    /**
     * @param $routeName
     * @param $routePass
     * @param $expected
     * @dataProvider gateProvider
     * @test
     */
    public function gate($routeName, $routePass, $expected)
    {
        $config = [];
        if ($routePass === null) {
            $config['gate'] = $routeName;
        } else {
            $config['gate'] = [
                'key' => $routeName,
                'pass' => $routePass,
            ];
        }
        $result = $this->runEvaluationProcess($config);
        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    // TODO test loop check
}
