<?php

namespace FormRelay\Core\Tests\Integration\Service;

use FormRelay\Core\Service\Relay;
use FormRelay\Core\Tests\Integration\RelayTestTrait;
use PHPUnit\Framework\TestCase;

class RelayTest extends TestCase
{
    use RelayTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initRelay();
    }

    /** @test */
    public function syncOneRouteOnePass() {
        $this->setSubmissionAsync(false);
        $this->addRouteSpy([
            'enabled' => true,
            'fields' => [
                'field1' => [ 'field' => 'field1' ],
                'field2' => [ 'field' => 'field2' ],
            ],
        ]);
        $this->submissionData = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        $this->routeSpy->expects($this->once())->method('send')->with([
            'field1' => 'value1',
            'field2' => 'value2',
        ]);
        $this->queue->expects($this->never())->method('addJob');

        $relay = new Relay($this->registry);
        $relay->process($this->getSubmission());
    }

    /** @test */
    public function asyncOneRouteOnePass() {
        $this->setSubmissionAsync(true);
        $this->addRouteSpy([
            'enabled' => true,
            'fields' => [
                'field1' => [ 'field' => 'field1' ],
                'field2' => [ 'field' => 'field2' ],
            ],
        ]);
        $this->submissionData = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        $this->queue->expects($this->once())->method('addJob')->with([
            'data' => [
                'field1' => ['type' => 'string', 'value' => 'value1'],
                'field2' => ['type' => 'string', 'value' => 'value2'],
            ],
            'configuration' => [[
                'async' => true,
                'routes' => [
                    'spiedOn' => [
                        'enabled' => true,
                        'fields' => [
                            'field1' => ['field' => 'field1'],
                            'field2' => ['field' => 'field2'],
                        ],
                    ],
                ],
                'dataProviders' => [],
            ]],
            'context' => [
                'job' => [
                    'route' => 'spiedOn',
                    'pass' => 0,
                ],
            ],
        ]);
        $this->routeSpy->expects($this->never())->method('send');

        $relay = new Relay($this->registry);
        $relay->process($this->getSubmission());
    }

    // TODO implement more tests
    //      - multiple passes
    //      - gate
    //        - gate referencing foreign route
    //      - data providers
}
