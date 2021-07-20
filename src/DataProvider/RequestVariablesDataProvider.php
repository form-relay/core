<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;

class RequestVariablesDataProvider extends DataProvider
{
    const KEY_VARIABLE_FIELD_MAP = 'variableFieldMap';
    const DEFAULT_VARIABLE_FIELD_MAP = [];

    protected function processContext(SubmissionInterface $submission, RequestInterface $request)
    {
        $variables = array_keys($this->getConfig(static::KEY_VARIABLE_FIELD_MAP));
        foreach ($variables as $variable) {
            $this->addRequestVariableToContext($submission, $request, $variable);
        }
    }

    protected function process(SubmissionInterface $submission)
    {
        $variableFieldMap = $this->getConfig(static::KEY_VARIABLE_FIELD_MAP);
        foreach ($variableFieldMap as $variable => $field) {
            $this->setFieldFromRequestVariable($submission, $variable, $field);
        }
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_VARIABLE_FIELD_MAP => static::DEFAULT_VARIABLE_FIELD_MAP,
        ];
    }
}
