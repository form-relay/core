<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;

interface EvaluationInterface extends ConfigurationResolverInterface
{
    const RESOLVER_TYPE = 'Evaluation';

    public function eval(array $keysEvaluated = []): bool;
}
