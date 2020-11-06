<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

class ValueMapFieldMapper extends FieldMapper
{
    public function prepare(array &$result)
    {
        $valueMapper = $this->registry->getValueMapper('general', $this->config, $this->context->copy());
        $this->context['value'] = $valueMapper->resolve();
    }
}
