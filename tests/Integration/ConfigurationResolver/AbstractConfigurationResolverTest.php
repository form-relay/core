<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver;

use FormRelay\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use FormRelay\Core\ConfigurationResolver\FieldTracker;
use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Tests\Integration\RegistryTestTrait;
use FormRelay\Core\Tests\Integration\SubmissionTestTrait;
use FormRelay\Core\Tests\MultiValueTestTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractConfigurationResolverTest extends TestCase
{
    use RegistryTestTrait;
    use SubmissionTestTrait;
    use MultiValueTestTrait;

    /** @var array */
    protected $configurationResolverContext;

    /** @var FieldTracker */
    protected $fieldTracker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initRegistry();
        $this->initSubmission();
        $this->configurationResolverContext = [];
        $this->fieldTracker = new FieldTracker();
    }

    abstract protected function getGeneralResolverClass(): string;

    protected function processResolver(GeneralConfigurationResolverInterface $resolver)
    {
        return $resolver->resolve();
    }

    protected function runResolverTest($config)
    {
        $submission = $this->getSubmission();
        $context = new ConfigurationResolverContext($submission, $this->configurationResolverContext, $this->fieldTracker);

        $resolverClass = $this->getGeneralResolverClass();
        $resolver = new $resolverClass($this->registry, $config, $context);

        return $this->processResolver($resolver);
    }
}
