<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

use FormRelay\Core\Model\Form\FieldInterface;

class IfValueMapper extends ValueMapper
{
    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    protected function resolveValue($fieldValue)
    {
        $result = $this->resolveEvaluation($this->configuration);
        if ($result !== null) {
            return $this->resolveValueMap($result, $fieldValue);
        }
        return null;
    }

    public function getWeight(): int
    {
        return -1;
    }
}
