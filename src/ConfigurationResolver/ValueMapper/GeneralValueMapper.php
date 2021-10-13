<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Model\Form\FieldInterface;

class GeneralValueMapper extends ValueMapper implements GeneralConfigurationResolverInterface
{
    protected function getConfigurationBehaviour(): int
    {
        return static::CONFIGURATION_BEHAVIOUR_CONVERT_SCALAR_TO_ARRAY_WITH_SELF_VALUE;
    }

    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    protected function resolveValue($fieldValue)
    {
        $valueMappers = [];
        foreach ($this->configuration as $key => $value) {
            // try to instantiate sub-mapper
            $valueMapper = $this->resolveKeyword($key, $value);

            // if not successful, create a general mapper as sub-mapper if the config key is the data value
            if (!$valueMapper && (string)$key === (string)$fieldValue) {
                $valueMapper = $this->resolveKeyword('general', $value);
            }

            if ($valueMapper) {
                $valueMappers[] = $valueMapper;
            }
        }

        $this->sortSubResolvers($valueMappers);

        foreach ($valueMappers as $valueMapper) {
            // calculate the result
            $result = $valueMapper->resolve($fieldValue);
            // if the result is not null (may be returned from an evaluation process without a then/else part)
            // then stop and return the result
            if ($result !== null) {
                return $result;
            }
        }

        // if no result was found, return the original value
        return parent::resolveValue($fieldValue);
    }
}
