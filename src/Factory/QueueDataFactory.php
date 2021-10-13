<?php

namespace FormRelay\Core\Factory;

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
        return new Submission($this->unpackData($data['data']), $data['configuration'], $data['context']);
    }
}
