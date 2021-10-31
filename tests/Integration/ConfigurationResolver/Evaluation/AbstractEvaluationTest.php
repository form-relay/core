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

    protected function processResolver(GeneralConfigurationResolverInterface $resolver)
    {
        if ($this->eval) {
            /** @var GeneralEvaluation $resolver */
            return $resolver->eval();
        } else {
            return parent::processResolver($resolver);
        }
    }

    protected function runResolverTest($config)
    {
        $this->eval = false;
        return parent::runResolverTest($config);
    }

    protected function runEvaluationTest($config)
    {
        $this->eval = true;
        return parent::runResolverTest($config);
    }
}
