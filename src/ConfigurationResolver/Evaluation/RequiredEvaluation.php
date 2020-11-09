<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\Model\Form\MultiValueField;

class RequiredEvaluation extends Evaluation
{
    protected function convertScalarConfigToArray()
    {
        return true;
    }

    public function eval(array $keysEvaluated = []): bool
    {
        $result = true;
        foreach ($this->config as $requiredField) {
            if (!isset($this->context['data'][$requiredField])) {
                $result = false;
                break;
            }
            if (!$this->context['data'][$requiredField]) {
                $result = false;
                break;
            }
            if (
                $this->context['data'][$requiredField] instanceof MultiValueField
                && count($this->context['data'][$requiredField]) === 0
            ) {
                $result = false;
                break;
            }
        }
        return $result;
    }
}
