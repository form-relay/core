<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

abstract class AbstractClauseEvaluation extends Evaluation
{
    const KEY_FIELD = 'field';
    const KEY_INDEX = 'index';
    const KEY_MODIFY = 'modify';

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    abstract protected function initialValue(): bool;

    abstract protected function calculate(bool $result, EvaluationInterface $evaluation, array $keysEvaluated): bool;

    public function eval(array $keysEvaluated = []): bool
    {
        $subEvaluations = [];

        foreach ($this->configuration as $key => $value) {
            if ($key === static::KEY_FIELD && !is_array($value)) {
                $this->addKeyToContext($value);
                continue;
            }
            if ($key === static::KEY_INDEX && !is_array($value)) {
                $this->addIndexToContext($value);
                continue;
            }
            if ($key === static::KEY_MODIFY) {
                $this->addModifierToContext($value);
                continue;
            }

            $evaluation = $this->resolveKeyword($key, $value);

            if (!$evaluation) {
                if (!is_numeric($key)) {
                    $this->addKeyToContext($key);
                }
                $evaluation = $this->resolveKeyword('general', $value);
            }

            $subEvaluations[] = $evaluation;
        }

        $this->sortSubResolvers($subEvaluations);

        $result = $this->initialValue();
        foreach ($subEvaluations as $evaluation) {
            $result = $this->calculate($result, $evaluation, $keysEvaluated);
        }
        return $result;
    }
}
