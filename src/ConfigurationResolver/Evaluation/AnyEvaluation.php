<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Model\Form\MultiValueField;

class AnyEvaluation extends Evaluation
{
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return $this->evaluate($this->configuration);
    }

    protected function initialValue(): bool
    {
        return false;
    }

    protected function calculateResult(bool $indexResult, bool $overallResult)
    {
        return $indexResult || $overallResult;
    }

    protected function evalMultiValue(MultiValueField $fieldValue, array $keysEvaluated = []): bool
    {
        $baseContext = $this->context;
        $result = $this->initialValue();
        foreach ($fieldValue as $index => $value) {
            $context = $baseContext->copy();
            $this->addIndexToContext($index, $context);
            $result = $this->calculateResult($this->evaluate($this->configuration, $context), $result);
        }
        return $result;
    }
}
