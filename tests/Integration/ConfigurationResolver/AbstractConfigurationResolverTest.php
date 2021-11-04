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

    protected function executeResolver(GeneralConfigurationResolverInterface $resolver)
    {
        return $resolver->resolve();
    }

    /**
     * This is the execution of the actual resolver process
     *
     * - build a submission based on the field data from $this->submissionData
     *   (and $this->submissionConfiguration and $this->>submissionContext)
     * - instantiate the general resolver
     * - let the general resolver process the given configuration array $config
     * - return the processed result so that it can be compared to the expected outcome
     *
     * @param $config
     * @return mixed
     */
    protected function runResolverProcess($config)
    {
        $submission = $this->getSubmission();
        $context = new ConfigurationResolverContext($submission, $this->configurationResolverContext, $this->fieldTracker);

        $resolverClass = $this->getGeneralResolverClass();
        $resolver = new $resolverClass($this->registry, $config, $context);

        return $this->executeResolver($resolver);
    }
}
