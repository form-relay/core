<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Service\RegisterableInterface;
use FormRelay\Core\Service\RegistryInterface;

interface DataProviderInterface extends RegisterableInterface
{
    public function __construct(RegistryInterface $registry, LoggerInterface $logger);
    public function addData(SubmissionInterface $submission);
}
