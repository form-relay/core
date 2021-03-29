<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

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

            if ($condition) {
                $context = $this->context->copy();
                $context['key'] = $key;
                if (!$this->evaluate($condition, $context)) {
                    continue;
                }
            }

            $context = $this->context->copy();
            $context['data'][$varKey] = $key;
            $context['data'][$varValue] = $value;
            $result[] = $this->resolveContent($template, $context);
            unset($context['data'][$varKey]);
            unset($context['data'][$varValue]);
        }
        return $this->resolveContent($result);
    }
}
