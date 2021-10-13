<?php

namespace FormRelay\Core\Route;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\RegisterableInterface;

interface RouteInterface extends RegisterableInterface
{
    public function getPassCount(SubmissionInterface $submission): int;

    /**
     * @param SubmissionInterface $submission
     * @param int $pass
     * @return bool
     * @throws FormRelayException
     */
    public function processPass(SubmissionInterface $submission, int $pass): bool;

    public function addContext(SubmissionInterface $submission, RequestInterface $request);

    public static function getDefaultConfiguration(): array;
}
