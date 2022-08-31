<?php

namespace FormRelay\Core\Tests\Spy\Route;

use FormRelay\Core\DataDispatcher\DataDispatcherInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;

interface RouteSpyInterface extends DataDispatcherInterface
{
    public function addContext(SubmissionInterface $submission, RequestInterface $request, int $pass);
}
