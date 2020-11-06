<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;

class IfContentResolver extends ContentResolver
{
    public function finish(string &$result): bool
    {
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->registry->getEvaluation('general', $this->config, $this->context->copy());
        $evalResult = $evaluation->resolve();
        if ($evalResult !== null) {
            /** @var GeneralContentResolver $contentResolver */
            $contentResolver = $this->resolveKeyword('general', $evalResult);
            $result = $contentResolver->resolve();
            return true;
        }
        return false;
    }

    public function getWeight(): int
    {
        return -1;
    }
}
