<?php

namespace FormRelay\Core\Tests\Spy\DataProvider;

use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;

interface DataProviderSpyInterface
{
    public function processContext(SubmissionInterface $submission, RequestInterface $request);
    public function process(SubmissionInterface $submission);
}
