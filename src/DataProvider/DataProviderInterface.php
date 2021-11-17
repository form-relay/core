<?php

namespace FormRelay\Core\DataProvider;

use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Plugin\PluginInterface;

interface DataProviderInterface extends PluginInterface
{
    public function addContext(SubmissionInterface $submission, RequestInterface $request);
    public function addData(SubmissionInterface $submission);

    public static function getDefaultConfiguration(): array;
}
