<?php

namespace FormRelay\Core\Tests\Integration\Service;

use FormRelay\Core\Service\Relay;
use FormRelay\Core\Tests\Integration\RelayTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers Relay
 */
class RelayTest extends TestCase
{
    use RelayTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initRelay();
    }

    /** @test */
    public function syncOneRouteOnePassWithStorage() {
        $this->setSubmissionAsync(false);
        $this->setStorageDisabled(false);
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

        $this->queue->expects($this->once())->method('addJob')->with([
            'data' => [
                'field1' => ['type' => 'string', 'value' => 'value1'],
                'field2' => ['type' => 'string', 'value' => 'value2'],
            ],
            'configuration' => [[
                'async' => false,
                'disableStorage' => false,
                'routes' => [
                    'generic' => [
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
                    'route' => 'generic',
                    'pass' => 0,
                    'label' => '08441#generic',
                ],
                'submission' => [
                    'hash' => '08441D5F547F10021A71D5BC4F7A6B0A',
                    'short-hash' => '08441',
                ],
            ],
        ]);

        $relay = new Relay($this->registry);
        $relay->process($this->getSubmission());
    }

    /** @test */
    public function syncOneRouteOnePassWithoutStorage() {
        $this->setSubmissionAsync(false);
        $this->setStorageDisabled(true);
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

        $this->queue->expects($this->never())->method('addJob');

        $this->routeSpy->expects($this->once())->method('send')->with([
            'field1' => 'value1',
            'field2' => 'value2',
        ]);

        $relay = new Relay($this->registry);
        $relay->process($this->getSubmission());
    }

    /** @test */
    public function asyncOneRouteOnePassWithStorage() {
        $this->setSubmissionAsync(true);
        $this->setStorageDisabled(false);
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
                'disableStorage' => false,
                'routes' => [
                    'generic' => [
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
                    'route' => 'generic',
                    'pass' => 0,
                    'label' => '27AAE#generic',
                ],
                'submission' => [
                    'hash' => '27AAE32C5E158859A2D7801669DE167B',
                    'short-hash' => '27AAE',
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
