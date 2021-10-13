<?php

namespace FormRelay\Core\ConfigurationResolver\Evaluation;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolver;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Form\MultiValueField;
use FormRelay\Core\Utility\GeneralUtility;

abstract class Evaluation extends ConfigurationResolver implements EvaluationInterface
{
    protected static function getResolverInterface(): string
    {
        return EvaluationInterface::class;
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

    /**
     * if a multi-value field is evaluated, a disjunction means that
     * the whole evaluation is true if at least one evaluation
     * for one of the values of that field is true (or-condition)
     *
     * @return bool
     */
    protected function multiValueIsDisjunctive()
    {
        return true;
    }

    protected function addKeyToContext($key)
    {
        $resolvedKey = $this->resolveContent($key);
        if (!GeneralUtility::isEmpty($resolvedKey)) {
            $this->context['key'] = (string)$resolvedKey;
        }
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
     * the method "eval" is called to evaluate the expression defined in the config
     * it will always return a boolean value
     *
     * @param array $keysEvaluated
     * @return bool
     */
    public function eval(array $keysEvaluated = []): bool
    {
        $fieldValue = null;
        if (isset($this->context['key'])) {
            if ($this->context['useKey'] ?? false) {
                $fieldValue = $this->context['key'];
            } else {
                $fieldValue = $this->getFieldValue($this->context['key']);
            }
        }

        if ($fieldValue instanceof MultiValueField) {
            if ($this->multiValueIsDisjunctive()) {
                $result = false;
                foreach ($fieldValue as $value) {
                    $result = $this->evalValue($value, $keysEvaluated) || $result;
                }
            } else {
                $result = true;
                foreach ($fieldValue as $value) {
                    $result = $this->evalValue($value, $keysEvaluated) && $result;
                }
            }
        } else {
            $result = $this->evalValue($fieldValue, $keysEvaluated);
        }
        return $result;
    }
}
