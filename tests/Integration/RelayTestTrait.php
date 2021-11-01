<?php

namespace FormRelay\Core\Tests\Integration;

trait RelayTestTrait
{
    use RegistryTestTrait;
    use SubmissionTestTrait;

    protected $routeSpy = null;
    protected $dataProviderSpy = null;

    protected function initRelay()
    {
        $this->initRegistry();
        $this->registerAllDefaults();
        $this->initSubmission();
        $this->routeSpy = null;
        $this->dataProviderSpy = null;
    }

    protected function addRouteSpy($configuration)
    {
        $this->routeSpy = $this->registerRouteSpy();
        $this->addRouteConfiguration('generic', $configuration);
        return $this->routeSpy;
    }

    protected function addDataProviderSpy($configuration)
    {
        $this->dataProviderSpy = $this->registerDataProviderSpy();
        $this->addDataProviderConfiguration('generic', $configuration);
        return $this->dataProviderSpy;
    }
}
