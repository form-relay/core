<?php

namespace FormRelay\Core\Tests\Integration;

use FormRelay\Core\Model\Queue\JobInterface;

trait JobTestTrait //  extends \PHPUnit\Framework\TestCase
{
    protected function createJob($data, $genericRouteConfig, $pass = 0, $dataProviderConfig = [], $context = [])
    {
        $job = $this->createMock(JobInterface::class);
        $packed = [
            'data' => $data,
            'configuration' => [
                [
                    'routes' => [
                        'generic' => $genericRouteConfig,
                    ],
                    'dataProviders' => $dataProviderConfig,
                ],
            ],
            'context' => $context,
        ];
        $packed['context']['job'] = [
            'route' => 'generic',
            'pass' => $pass,
        ];

        $job->expects($this->any())->method('getData')->willReturn($packed);
        return $job;
    }
}
