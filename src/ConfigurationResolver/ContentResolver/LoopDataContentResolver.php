<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\Evaluation\GeneralEvaluation;

class LoopDataContentResolver extends ContentResolver
{
    const KEY_GLUE = 'glue';

    const KEY_TEMPLATE = 'template';

    const KEY_VAR_KEY = 'asKey';
    const DEFAULT_VAR_KEY = 'key';

    const KEY_VAR_VALUE = 'as';
    const DEFAULT_VAR_VALUE = 'value';

    const KEY_CONDITION = 'condition';
    const DEFAULT_CONDITION = false;

    public function build()
    {
        if (!is_array($this->config)) {
            $this->config = is_string($this->config) ? [static::KEY_TEMPLATE => $this->config] : [];
        }
        $glue = $this->config[static::KEY_GLUE] ?? false;

        $varKey = $this->config[static::KEY_VAR_KEY] ?? static::DEFAULT_VAR_KEY;
        $varValue = $this->config[static::KEY_VAR_VALUE] ?? static::DEFAULT_VAR_VALUE;

        $template = $this->config[static::KEY_TEMPLATE] ?? false;
        if (empty($template) || $template === true) {
            $template = [
                'self' => '{' . $varKey . '}\s=\s{' . $varValue . '}\n',
                'insertData' => true
            ];
        }

        $condition = $this->config[static::KEY_CONDITION] ?? static::DEFAULT_CONDITION;

        // don't allow overrides of form data
        if ($this->fieldExists($varKey) || $this->fieldExists($varValue)) {
            return '';
        }

        $result = [];
        if ($glue) {
            $result[static::KEYWORD_GLUE] = $glue;
        }
        foreach ($this->context['data'] as $key => $value) {
            $context = $this->context->copy();
            $context['data'][$varKey] = $key;
            $context['data'][$varValue] = $value;
            $context['key'] = $key;
            if ($condition) {
                if (!$this->evaluate($condition, $context)) {
                    continue;
                }
            }
            /** @var GeneralContentResolver $contentResolver */
            $contentResolver = $this->resolveKeyword('general', $template, $context);
            $result[] = $contentResolver->resolve();
            unset($context['data'][$varKey]);
            unset($context['data'][$varValue]);
            unset($context['key']);
        }
        $contentResolver = $this->resolveKeyword('general', $result);
        return $contentResolver->resolve();
    }
}
