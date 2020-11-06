<?php

namespace FormRelay\Core\Route;

use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Service\RegisterableInterface;
use FormRelay\Core\Service\RegistryInterface;

interface RouteInterface extends RegisterableInterface
{
    public function __construct(RegistryInterface $registry, LoggerInterface $logger);
    public function process(SubmissionInterface $submission): bool;
    public static function getDefaultConfiguration(): array;
}
