<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

class DistributeFieldMapper extends FieldMapper
{
    public function convertScalarConfigToArray()
    {
        return true;
    }

    public function finish(array &$result): bool
    {
        foreach ($this->config as $field) {
            /** @var GeneralFieldMapper $fieldMapper */
            $fieldMapper = $this->resolveKeyword('general', $field);
            $result = $fieldMapper->resolve($result);
        }
        return true;
    }
}
