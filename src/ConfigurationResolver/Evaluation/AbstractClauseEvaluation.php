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

        if (array_key_exists(static::KEY_MODIFY, $this->configuration)) {
            $this->addModifierToContext($this->configuration[static::KEY_MODIFY]);
            unset($this->configuration[static::KEY_MODIFY]);
        }

        if (array_key_exists(static::KEY_FIELD, $this->configuration) && !is_array($this->configuration[static::KEY_FIELD])) {
            $this->addKeyToContext($this->configuration[static::KEY_FIELD]);
            unset($this->configuration[static::KEY_FIELD]);
        }

        if (array_key_exists(static::KEY_INDEX, $this->configuration) && !is_array($this->configuration[static::KEY_INDEX])) {
            $this->addIndexToContext($this->configuration[static::KEY_INDEX]);
            unset($this->configuration[static::KEY_INDEX]);
        }

        foreach ($this->configuration as $key => $value) {
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
