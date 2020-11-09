<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Service\RegisterableInterface;

interface DataProviderInterface extends RegisterableInterface
{
    public function addData(SubmissionInterface $submission);
    public static function getDefaultConfiguration(): array;
}
