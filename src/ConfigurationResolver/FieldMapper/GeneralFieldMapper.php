<?php

namespace FormRelay\Core\ConfigurationResolver\FieldMapper;

use FormRelay\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use FormRelay\Core\Model\Submission\SubmissionConfigurationInterface;

class GeneralFieldMapper extends FieldMapper implements GeneralConfigurationResolverInterface
{
    protected $fieldMappers = [];

    public function resolve(array $result = []): array
    {
        if (!is_array($this->config)) {
            $this->config = [SubmissionConfigurationInterface::KEY_CONTENT => $this->config];
        }

        $this->fieldMappers = [];
        foreach ($this->config as $key => $value) {
            $fieldMapper = $this->resolveKeyword($key, $value, $this->context);
            if (!$fieldMapper && is_numeric($key)) {
                $fieldMapper = $this->resolveKeyword('general', $value, $this->context);
            }
            if ($fieldMapper) {
                $this->fieldMappers[] = $fieldMapper;
            }
        }
        $this->sortSubResolvers($this->fieldMappers);

        $this->prepare($result);
        $this->finish($result);
        return $result;
    }

    public function prepare(array &$result)
    {
        foreach ($this->fieldMappers as $fieldMapper) {
            $fieldMapper->prepare($result);
        }
    }

    public function finish(array &$result): bool
    {
        foreach ($this->fieldMappers as $fieldMapper) {
            if ($fieldMapper->finish($result)) {
                return true;
            }
        }
        return false;
    }
}
