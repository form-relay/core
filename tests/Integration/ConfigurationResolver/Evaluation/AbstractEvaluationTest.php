<?php

namespace FormRelay\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

abstract class AbstractEvaluationTest extends AbstractConfigurationResolverTest
{
    protected $eval = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registerBasicEvaluations();
        $this->registerBasicContentResolvers();
    }

    protected function getGeneralResolverClass(): string
    {
        return GeneralEvaluation::class;
    }

    protected function executeResolver(GeneralConfigurationResolverInterface $resolver)
    {
        if ($this->eval) {
            /** @var GeneralEvaluation $resolver */
            return $resolver->eval();
        } else {
            return parent::executeResolver($resolver);
        }
    }

    protected function runResolverProcess($config)
    {
        $this->eval = false;
        return parent::runResolverProcess($config);
    }

    protected function runEvaluationProcess($config)
    {
        $this->eval = true;
        return parent::runResolverProcess($config);
    }
}
