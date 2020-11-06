<?php

namespace FormRelay\Core\ConfigurationResolver\ContentResolver;

use FormRelay\Core\ConfigurationResolver\ConfigurationResolver;
use FormRelay\Core\Model\Form\MultiValueField;

abstract class ContentResolver extends ConfigurationResolver implements ContentResolverInterface
{
    const KEYWORD_GLUE = 'glue';

    protected static function getResolverInterface(): string
    {
        return ContentResolverInterface::class;
    }

    protected function getFieldValue($key)
    {
        $fieldValue = isset($this->context['data'][$key])
            ? $this->context['data'][$key]
            : '';
        if ($fieldValue instanceof MultiValueField && isset($this->context[static::KEYWORD_GLUE])) {
            $fieldValue = $fieldValue->__toString(static::KEYWORD_GLUE);
        }
        return $fieldValue;
    }

    public function build(): string
    {
        return '';
    }

    public function finish(string &$result): bool
    {
        return false;
    }
}
