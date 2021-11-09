<?php

namespace FormRelay\Core\Factory;

use FormRelay\Core\Exception\FormRelayException;
use FormRelay\Core\Model\Form\FieldInterface;
use FormRelay\Core\Model\Queue\Job;
use FormRelay\Core\Model\Queue\JobInterface;
use FormRelay\Core\Model\Submission\Submission;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Queue\QueueInterface;
use FormRelay\Core\Utility\GeneralUtility;
use InvalidArgumentException;

class QueueDataFactory implements QueueDataFactoryInterface
{
    const KEY_ROUTE = 'route';
    const DEFAULT_ROUTE = 'undefined';

    const KEY_PASS = 'pass';
    const DEFAULT_PASS = 0;

    const KEY_SUBMISSION = 'submission';
    const DEFAULT_SUBMISSION = [];

    const DEFAULT_LABEL = 'undefined';

    protected function createJob(): JobInterface
    {
        return new Job();
    }

    protected function getSubmissionDataHash(array $submissionData): string
    {
        return GeneralUtility::calculateHash($submissionData);
    }

    public function getSubmissionHash(SubmissionInterface $submission): string
    {
        return $this->getSubmissionDataHash($this->pack($submission));
    }

    public function getJobHash(JobInterface $job): string
    {
        $this->updateLegacyJobData($job);
        return $this->getSubmissionDataHash($this->getJobSubmissionData($job));
    }

    protected function getSubmissionDataLabel(array $submissionData, string $route, int $pass, string $hash = ''): string
    {
        if (!$hash) {
            $hash = $this->getSubmissionDataHash($submissionData);
        }
        try {
            $submission = $this->unpack($submissionData);
            return $this->getSubmissionLabel($submission, $route, $pass, $hash);
        } catch (FormRelayException $e) {
            return static::DEFAULT_LABEL;
        }
    }

    public function getSubmissionLabel(SubmissionInterface $submission, string $route, int $pass, string $hash = ''): string
    {
        if (!$hash) {
            $hash = $this->getSubmissionHash($submission);
        }
        return GeneralUtility::shortenHash($hash)
            . '#' . $submission->getConfiguration()->getRoutePassLabel($route, $pass);
    }

    public function getJobLabel(JobInterface $job): string
    {
        $this->updateLegacyJobData($job);
        return $this->getSubmissionDataLabel(
            $this->getJobSubmissionData($job),
            $this->getJobRoute($job),
            $this->getJobRoutePass($job),
            $job->getHash()
        );
    }

    protected function getJobSubmissionData(JobInterface $job): array
    {
        $this->updateLegacyJobData($job);
        return $job->getData()[static::KEY_SUBMISSION] ?? static::DEFAULT_SUBMISSION;
    }

    public function getJobRoutePass(JobInterface $job): int
    {
        $this->updateLegacyJobData($job);
        return $job->getData()[static::KEY_PASS] ?? static::DEFAULT_PASS;
    }

    public function getJobRoute(JobInterface $job): string
    {
        $this->updateLegacyJobData($job);
        return $job->getData()[static::KEY_ROUTE] ?? static::DEFAULT_ROUTE;
    }

    public function convertSubmissionToJob(SubmissionInterface $submission, string $route, int $pass, int $status = QueueInterface::STATUS_PENDING): JobInterface
    {
        $submissionData = $this->pack($submission);
        $job = $this->createJob();
        $job->setStatus($status);
        $job->setData([
            static::KEY_ROUTE => $route,
            static::KEY_PASS => $pass,
            static::KEY_SUBMISSION => $submissionData,
        ]);
        $job->setHash($this->getSubmissionDataHash($submissionData));
        $job->setLabel($this->getSubmissionLabel($submission, $route, $pass, $job->getHash()));
        return $job;
    }

    /**
     * @param JobInterface $job
     * @return SubmissionInterface
     * @throws FormRelayException
     */
    public function convertJobToSubmission(JobInterface $job): SubmissionInterface
    {
        $this->updateLegacyJobData($job);
        return $this->unpack($this->getJobSubmissionData($job));
    }

    protected function packData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if ($value instanceof FieldInterface) {
                    $type = get_class($value);
                    $packedValue = $value->pack();
                } else {
                    throw new InvalidArgumentException('Invalid field class "' . get_class($value) . '"');
                }
            } else {
                $type = 'string';
                $packedValue = (string)$value;
            }
            $result[$key] = [
                'type' => $type,
                'value' => $packedValue,
            ];
        }
        return $result;
    }

    protected function pack(SubmissionInterface $submission): array
    {
        return [
            'data' => $this->packData($submission->getData()->toArray()),
            'configuration' => $submission->getConfiguration()->toArray(),
            'context' => $submission->getContext()->toArray(),
        ];
    }

    protected function unpackData(array $packedData): array
    {
        $result = [];
        foreach ($packedData as $key => $packedValue) {
            if ($packedValue['type'] === 'string') {
                $result[$key] = $packedValue['value'];
            } else {
                if (!class_exists($packedValue['type'])) {
                    throw new FormRelayException('Unknown class "' . $packedValue['type'] . '"');
                }
                if (!in_array(FieldInterface::class, class_implements($packedValue['type']))) {
                    throw new FormRelayException('Invalid field class "' . $packedValue['type'] . '"');
                }
                $result[$key] = $packedValue['type']::unpack($packedValue['value']);
            }
        }
        return $result;
    }

    /**
     * @param array $data
     * @throws FormRelayException
     */
    protected function validatePackage(array $data)
    {
        if (!$data || !is_array($data) || empty($data)) {
            throw new FormRelayException('job data is empty');
        }
        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new FormRelayException('job has no valid submission data');
        }
        if (!isset($data['configuration']) || !is_array($data['configuration'])) {
            throw new FormRelayException('job has no valid submission configuration');
        }
        if (!isset($data['context']) || !is_array($data['context'])) {
            throw new FormRelayException('job has no valid submission context');
        }
    }

    /**
     * @param array $data
     * @return SubmissionInterface
     * @throws FormRelayException
     */
    protected function unpack(array $data): SubmissionInterface
    {
        $this->validatePackage($data);
        return new Submission($this->unpackData($data['data']), $data['configuration'], $data['context']);
    }

    public function getSubmissionCacheKey(SubmissionInterface $submission): string
    {
        return serialize($this->pack($submission));
    }

    /**
     * Taking legacy data structure into account
     *
     * @param JobInterface $job
     * @return bool
     */
    public function updateLegacyJobData(JobInterface $job): bool
    {
        $data = $job->getData();
        if (empty($data)) {
            return false;
        }

        if (isset($data['context']['job'])) {
            $route = $data['context']['job']['route'] ?? static::DEFAULT_ROUTE;
            $pass = $data['context']['job']['pass'] ?? static::DEFAULT_PASS;
            unset($data['context']['job']);
            $job->setData([
                static::KEY_ROUTE => $route,
                static::KEY_PASS => $pass,
                static::KEY_SUBMISSION => $data,
            ]);
        }
        return true;
    }
}
