<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;

class IfFieldMapper extends FieldMapper
{
    public function finish(array &$result): bool
    {
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->registry->getEvaluation('general', $this->config, $this->context->copy());
        $evalResult = $evaluation->resolve();
        if ($evalResult !== null) {
            /** @var GeneralFieldMapper $fieldMapper */
            $fieldMapper = $this->resolveKeyword('general', $evalResult);
            $result = $fieldMapper->resolve($result);
            return true;
        }
        return false;
    }

    public function getWeight(): int
    {
        return -1;
    }
}
