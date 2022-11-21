<?php

namespace FormRelay\Core\Tests\Integration;

trait RelayTestTrait
{
    use RegistryTestTrait;
    use SubmissionTestTrait;
    use JobTestTrait;

    protected $routeSpy = null;
    protected $dataProviderSpy = null;

    protected function initRelay()
    {
        $this->initRegistry();
        $this->initSubmission();
        $this->routeSpy = null;
        $this->dataProviderSpy = null;
    }

    protected function addRouteSpy($configuration)
    {
        $this->registerRouteSpy();
        $this->addRouteConfiguration('generic', $configuration);
        return $this->routeSpy;
    }

    protected function addDataProviderSpy($configuration)
    {
        $this->registerDataProviderSpy();
        $this->addDataProviderConfiguration('generic', $configuration);
        return $this->dataProviderSpy;
    }
}
