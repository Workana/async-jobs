<?php
namespace Workana\AsyncJobs\Retry;

use Bernard\Envelope;
use Exception;
use Throwable;
use Workana\AsyncJobs\JobManager;

/**
 * Abstract retry strategy
 *
 * @author Carlos Frutos <charly@workana.com>
 */
abstract class RetryStrategy
{
    /**
     * @var JobManager
     */
    protected $jm;

    /**
     * Assign Job Manager
     *
     * @param JobManager $jm
     */
    public function setJobManager(JobManager $jm)
    {
        $this->jm = $jm;
    }

    /**
     * Handle job retry
     *
     * @param Envelope $envelope
     * @param Throwable|Exception $error Error
     *
     * @return void
     */
    public abstract function handleRetry(Envelope $envelope, $error);
}