<?php

namespace FormRelay\Core\Service;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Route\RouteInterface;

class Relay extends AbstractRelay
{
    public function process(SubmissionInterface $submission)
    {
        $async = $submission->getConfiguration()->get('async', false);

        $this->addContext($submission);
        if (!$async) {
            $this->processDataProviders($submission);
        }

        $routes = $this->registry->getRoutes();
        /**
         * @var string $routeName
         * @var RouteInterface $route
         */
        foreach ($routes as $routeName => $route) {
            $passCount = $route->getPassCount($submission);
            for ($pass = 0; $pass < $passCount; $pass++) {
                if ($async) {
                    $this->addJobToQueue($submission, $route::getKeyword(), $pass);
                } else {
                    try {
                        $route->processPass($submission, $pass);
                    } catch (FormRelayException $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            }
        }
    }
}
