<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

class AndEvaluation extends Evaluation
{
    const KEY_FIELD = 'field';

    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    protected function initialValue(): bool
    {
        return true;
    }

    protected function calculate(bool $result, EvaluationInterface $evaluation, array $keysEvaluated): bool
    {
        return $result && $evaluation->eval($keysEvaluated);
    }

    public function eval(array $keysEvaluated = []): bool
    {
        $subEvaluations = [];

        foreach ($this->configuration as $key => $value) {
            if ($key === static::KEY_FIELD) {
                $resolvedKey = $this->resolveContent($value);
                if ($resolvedKey !== null && $resolvedKey !== '') {
                    $this->context['key'] = $resolvedKey;
                }
                continue;
            }

            $evaluation = $this->resolveKeyword($key, $value);

            if (!$evaluation) {
                if (is_numeric($key)) {
                    $evaluation = $this->resolveKeyword('general', $value);
                } else {
                    /**
                     * The context can change from evaluation to evaluation
                     * but all evaluations are called at the end
                     * with the last context
                     */
                    $this->context['key'] = $key;
                    if (is_array($value)) {
                        $evaluation = $this->resolveKeyword('general', $value);
                    } else {
                        $evaluation = $this->resolveKeyword('equals', $value);
                    }
                }
            }

            if ($evaluation) {
                $subEvaluations[] = $evaluation;
            }
        }

        $this->sortSubResolvers($subEvaluations);

        $result = $this->initialValue();
        foreach ($subEvaluations as $evaluation) {
            $result = $this->calculate($result, $evaluation, $keysEvaluated);
        }
        return $result;
    }
}
