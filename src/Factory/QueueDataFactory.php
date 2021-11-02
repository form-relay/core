<?php

namespace FormRelay\Core\Factory;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Submission\Submission;
use FormRelay\Core\Model\Submission\SubmissionInterface;

class QueueDataFactory implements QueueDataFactoryInterface
{
    protected function packData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if ($value instanceof FieldInterface) {
                $type = get_class($value);
                $packedValue = $value->pack();
            } else {
                $type = 'string';
                $packedValue = (string)$value;
            }
            $result[$key] = [
                'type' => $type,
                'value' => $packedValue,
            ];
        }
        return $result;
    }

    public function pack(SubmissionInterface $submission): array
    {
        return [
            'data' => $this->packData($submission->getData()->toArray()),
            'configuration' => $submission->getConfiguration()->toArray(),
            'context' => $submission->getContext()->toArray(),
        ];
    }

    protected function unpackData(array $packedData): array
    {
        $result = [];
        foreach ($packedData as $key => $packedValue) {
            if ($packedValue['type'] === 'string') {
                $result[$key] = $packedValue['value'];
            } else {
                $result[$key] = $packedValue['type']::unpack($packedValue['value']);
            }
        }
        return $result;
    }

    public function unpack(array $data): SubmissionInterface
    {
        if (!$data || !is_array($data) || empty($data)) {
            throw new FormRelayException('job data is empty');
        }
        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new FormRelayException('job has no valid submission data');
        }
        if (!isset($data['configuration']) || !is_array($data['configuration'])) {
            throw new FormRelayException('job has no valid submission configuration');
        }
        if (!isset($data['context']) || !is_array($data['context'])) {
            throw new FormRelayException('job has no valid submission context');
        }
        return new Submission($this->unpackData($data['data']), $data['configuration'], $data['context']);
    }
}
