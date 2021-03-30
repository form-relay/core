<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class LoopDataContentResolver extends ContentResolver
{
    const KEY_GLUE = 'glue';
    const DEFAULT_GLUE = false;

    const KEY_TEMPLATE = 'template';
    const DEFAULT_TEMPLATE = '';

    const KEY_VAR_KEY = 'asKey';
    const DEFAULT_VAR_KEY = 'key';

    const KEY_VAR_VALUE = 'as';
    const DEFAULT_VAR_VALUE = 'value';

    const KEY_CONDITION = 'condition';
    const DEFAULT_CONDITION = false;

    public function build()
    {
        if (!is_array($this->configuration)) {
            $this->configuration = is_string($this->configuration) ? [static::KEY_TEMPLATE => $this->configuration] : [];
        }

        $glue = $this->getConfig(static::KEY_GLUE);
        $varKey = $this->getConfig(static::KEY_VAR_KEY);
        $varValue = $this->getConfig(static::KEY_VAR_VALUE);

        $template = $this->getConfig(static::KEY_TEMPLATE);
        if (empty($template) || $template === true) {
            $template = [
                SubmissionConfigurationInterface::KEY_SELF => '{' . $varKey . '}\s=\s{' . $varValue . '}\n',
                'insertData' => true
            ];
        }

        $condition = $this->getConfig(static::KEY_CONDITION);

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

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_GLUE => static::DEFAULT_GLUE,
            static::KEY_TEMPLATE => static::DEFAULT_TEMPLATE,
            static::KEY_VAR_KEY => static::DEFAULT_VAR_KEY,
            static::KEY_VAR_VALUE => static::DEFAULT_VAR_VALUE,
            static::KEY_CONDITION => static::DEFAULT_CONDITION,
        ];
    }


}
