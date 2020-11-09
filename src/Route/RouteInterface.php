<?php

namespace FormRelay\Core\Route;

use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Service\RegisterableInterface;

interface RouteInterface extends RegisterableInterface
{
    public function process(SubmissionInterface $submission): bool;
    public static function getDefaultConfiguration(): array;
}
