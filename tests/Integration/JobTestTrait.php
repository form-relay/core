<?php

namespace FormRelay\Core\Tests\Integration;

use FormRelay\Core\Factory\QueueDataFactory;
use FormRelay\Core\Model\Queue\JobInterface;

trait JobTestTrait //  extends \PHPUnit\Framework\TestCase
{
    protected function createJob($data, $genericRouteConfig, $pass = 0, $config = [], $context = [])
    {
        $job = $this->createMock(JobInterface::class);
        $packed = [
            QueueDataFactory::KEY_ROUTE => 'generic',
            QueueDataFactory::KEY_PASS => $pass,
            QueueDataFactory::KEY_SUBMISSION => [
                'data' => $data,
                'configuration' => $config,
                'context' => $context,
            ]
        ];
        $packed[QueueDataFactory::KEY_SUBMISSION]['configuration'][0]['routes']['generic'] = $genericRouteConfig;
        $job->expects($this->any())->method('getData')->willReturn($packed);
        return $job;
    }
}
