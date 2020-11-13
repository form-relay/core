<?php

namespace FormRelay\Core\ConfigurationResolver\ValueMapper;

use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class SwitchValueMapper extends ValueMapper
{
    const KEY_CASE = 'case';
    const KEY_VALUE = 'value';

    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    public function resolveValue($fieldValue)
    {
        $valueMapper = null;
        foreach ($this->config as $case) {
            $caseValue = $case[static::KEY_CASE] ?? ($case[SubmissionConfigurationInterface::KEY_SELF] ?? '');
            $caseResult = $case[static::KEY_VALUE] ?? '';
            if ($caseValue === $fieldValue) {
                /** @var GeneralValueMapper $valueMapper */
                $valueMapper = $this->resolveKeyword('general', $caseResult);
                break;
            }
        }
        if ($valueMapper) {
            return $valueMapper->resolve($fieldValue);
        }
        return parent::resolveValue($fieldValue);
    }
}
