<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolver;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;
use FormRelay\Core\Utility\GeneralUtility;

abstract class Evaluation extends ConfigurationResolver implements EvaluationInterface
{
    protected static function getResolverInterface(): string
    {
        return EvaluationInterface::class;
    }

    protected function addModifierToContext($modifier, $context = null)
    {
        if ($context === null) {
            $context = $this->context;
        }
        if (is_array($modifier)) {
            foreach ($modifier as $modifierKey => $modifierValue) {
                $context['modifier'][$modifierKey] = $modifierValue;
            }
        } else {
            $modifiers = GeneralUtility::castValueToArray($modifier);
            foreach ($modifiers as $modifierKey) {
                $context['modifier'][$modifierKey] = true;
            }
        }
    }

    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    protected function modifyValue($fieldValue)
    {
        $modifierConfig = $this->context['modifier'] ?? null;
        if ($modifierConfig) {
            $modifierConfig[SubmissionConfigurationInterface::KEY_SELF] = $fieldValue;
            $fieldValue = $this->resolveContent($modifierConfig);
        }
        return $fieldValue;
    }

    protected function getSelectedValue($context = null)
    {
        return $this->modifyValue(parent::getSelectedValue($context));
    }

    /**
     * @param string|FieldInterface|null $fieldValue
     * @param array $keysEvaluated
     * @return bool
     */
    protected function evalValue($fieldValue, array $keysEvaluated = [])
    {
        return true;
    }

    protected function evalMultiValue(MultiValueField $fieldValue, array $keysEvaluated = []): bool
    {
        return $this->evalValue($fieldValue, $keysEvaluated);
    }

    /**
     * the method "eval" is called to evaluate the expression defined in the config
     * it will always return a boolean value
     *
     * @param array $keysEvaluated
     * @return bool
     */
    public function eval(array $keysEvaluated = []): bool
    {
        $fieldValue = $this->getSelectedValue();

        if ($fieldValue instanceof MultiValueField) {
            $result = $this->evalMultiValue($fieldValue, $keysEvaluated);
        } else {
            $result = $this->evalValue($fieldValue, $keysEvaluated);
        }
        return $result;
    }
}
