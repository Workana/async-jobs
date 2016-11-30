<?php
namespace Workana\AsyncJobs\Retry;

use InvalidArgumentException;
use Bernard\Envelope;
use Workana\AsyncJobs\Job;

/**
 * Binary Exponential Backoff is an algorithm that uses feedback to multiplicatively
 * decrease the rate of some process, in order to gradually find an acceptable rate.
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class BinaryExponentialBackoff extends RetryStrategy
{
    /**
     * {@inheritDoc}
     */
    public function handleRetry(Envelope $envelope, $error)
    {
        $job = $envelope->getMessage();

        if (!($job instanceof Job)) {
            throw new InvalidArgumentException('Envelope message must have a valid job instance');
        }

        if (!$job->areRetriesExhausted()) {
            $job->incrementRetries();
            $this->redispatch($job);
        }
    }

    /**
     * Redispatch job
     *
     * @param Job $job
     *
     * @return void
     */
    private function redispatch(Job $job)
    {
        $delay = pow(2, $job->getRetries());

        $job->withDelay($delay);

        $this->jm->dispatch($job);
    }
}