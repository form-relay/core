<?php

namespace FormRelay\Core\Factory;

use FormRelay\Core\Model\Submission\Submission;
use FormRelay\Core\Model\Submission\SubmissionInterface;

class QueueDataFactory implements QueueDataFactoryInterface
{
    public function pack(SubmissionInterface $submission): array
    {
        return [
            'data' => $submission->getData()->toArray(),
            'configuration' => $submission->getConfiguration()->toArray(),
            'context' => $submission->getContext()->toArray(),
        ];
    }

    public function unpack(array $data): SubmissionInterface
    {
        return new Submission($data['data'], $data['configuration'], $data['context']);
    }
}
