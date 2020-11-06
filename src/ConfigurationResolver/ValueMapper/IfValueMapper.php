<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;
use FormRelay\Core\Model\Form\FieldInterface;

class IfValueMapper extends ValueMapper
{
    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    protected function resolveValue($fieldValue)
    {
        /** @var GeneralEvaluation $evaluation */
        $evaluation = $this->registry->getEvaluation('general', $this->config, $this->context->copy());
        if ($evaluation) {
            $result = $evaluation->resolve();
            if ($result !== null) {
                /** @var GeneralValueMapper $valueMapper */
                $valueMapper = $this->resolveKeyword('general', $result);
                return $valueMapper->resolve($fieldValue);
            }
        }
        return null;
    }

    public function getWeight(): int
    {
        return -1;
    }
}
