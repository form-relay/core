<?php

namespace FormRelay\Core\Factory;

use FormRelay\Core\Model\Queue\JobInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Queue\QueueInterface;

interface QueueDataFactoryInterface
{
    /**
     * Returns a hash built over the form data and the context of the submission.
     * Does not contain submission configuration.
     *
     * @param SubmissionInterface $submission
     * @return string
     */
    public function getSubmissionHash(SubmissionInterface $submission): string;

    /**
     * Returns a hash built over the form data and the context of the submission.
     * Does not contain submission configuration or the route name or the route pass.
     *
     * @param JobInterface $job
     * @return string
     */
    public function getJobHash(JobInterface $job): string;

    /**
     * Returns the label of a set of a submission, a route name and a route pass to a label used in jobs.
     *
     * @param SubmissionInterface $submission
     * @param string $route
     * @param int $pass
     * @param string $hash
     * @return string
     */
    public function getSubmissionLabel(SubmissionInterface $submission, string $route, int $pass, string $hash = ''): string;

    /**
     * Returns the label of the job.
     *
     * @param JobInterface $job
     * @return string
     */
    public function getJobLabel(JobInterface $job): string;

    /**
     * Converts a set of a submission, a route name and a route pass to a job.
     * Additionally the initial status of the job can be passed too.
     *
     * @param SubmissionInterface $submission
     * @param string $route
     * @param int $pass
     * @param int $status
     * @return JobInterface
     */
    public function convertSubmissionToJob(SubmissionInterface $submission, string $route, int $pass, int $status = QueueInterface::STATUS_PENDING): JobInterface;

    /**
     * Converts a job to a submission.
     *
     * @param JobInterface $job
     * @return SubmissionInterface
     */
    public function convertJobToSubmission(JobInterface $job): SubmissionInterface;

    /**
     * Returns the route pass of a job.
     *
     * @param JobInterface $job
     * @return int
     */
    public function getJobRoutePass(JobInterface $job): int;

    /**
     * Returns the route name of a job.
     *
     * @param JobInterface $job
     * @return string
     */
    public function getJobRoute(JobInterface $job): string;

    /**
     * Returns a cache key for the submission built over the form data, context and configuration of the submission.
     * Unlike the hash it does contain the submission configuration.
     * It is used to cache actions made on submissions (like processing of data providers).
     *
     * @param SubmissionInterface $submission
     * @return string
     */
    public function getSubmissionCacheKey(SubmissionInterface $submission): string;

    /**
     * Update job data in old format.
     * { data: {...}, configuration: [...], context: { job: { route:ROUTE, pass:PASS },... } }
     * >>
     * { route: ROUTE, pass: PASS, submission: { data: {...}, configuration: [...], context: {...} } }
     *
     * @param JobInterface $job
     */
    public function updateLegacyJobData(JobInterface $job);
}
