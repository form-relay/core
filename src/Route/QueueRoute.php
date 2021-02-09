<?php

namespace FormRelay\Core\Route;

use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Service\RegistryInterface;

class QueueRoute implements RouteInterface
{
    protected $registry;
    protected $route;

    public function __construct(RegistryInterface $registry, RouteInterface $route)
    {
        $this->registry = $registry;
        $this->route = $route;
    }

    public function getPassCount(SubmissionInterface $submission): int
    {
        return $this->route->getPassCount($submission);
    }

    public function processPass(SubmissionInterface $submission, int $pass): bool
    {
        $submission->getContext()['job']['route'] = $this->route::getKeyword();
        $submission->getContext()['job']['pass'] = $pass;
        $jobData = $this->registry->getQueueDataFactory()->pack($submission);
        $this->registry->getQueue()->addJob($jobData);
    }

    public static function getDefaultConfiguration(): array
    {
        return [];
    }

    public static function getKeyword(): string
    {
        return '';
    }

    public function getWeight(): int
    {
        return $this->route->getWeight();
    }
}
