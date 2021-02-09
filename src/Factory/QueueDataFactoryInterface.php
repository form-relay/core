<?php

namespace FormRelay\Core\Factory;

use FormRelay\Core\Model\Submission\SubmissionInterface;

interface QueueDataFactoryInterface
{
    public function pack(SubmissionInterface $submission): array;
    public function unpack(array $data): SubmissionInterface;
}
