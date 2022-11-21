<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolverInterface;

interface EvaluationInterface extends ConfigurationResolverInterface
{
    public function eval(array $keysEvaluated = []): bool;
}
