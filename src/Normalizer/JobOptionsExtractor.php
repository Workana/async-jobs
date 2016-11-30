<?php
namespace Workana\AsyncJobs\Normalizer;

use InvalidArgumentException;
use Workana\AsyncJobs\Job;

/**
 * Extracts and hydrates options of base Job, to be used on normalizers
 *
 * @author Carlos Frutos <charly@workana.com>
 */
trait JobOptionsExtractor
{
    /**
     * @param Job $job
     *
     * @return array Options
     */
    public function extractOptions(Job $job)
    {
        return [
            'delay' => $job->getDelay(),
            'retries' => $job->getRetries(),
            'maxRetries' => $job->getMaxRetries(),
            'preferredQueueName' => $job->getPreferredQueueName(),
        ];
    }

    /**
     * @param Job $job
     * @param array $options
     *
     * @return void
     */
    public function hydrateOptions(Job $job, array $options)
    {
        $missingOptions = array_diff([
            'delay',
            'retries',
            'maxRetries',
            'preferredQueueName'
        ], array_keys($options));

        if (!empty($missingOptions)) {
            throw new InvalidArgumentException(strtr('The following options are missing: :missingOptions', [
                ':missingOptions' => implode(', ', $missingOptions)
            ]));
        }

        $job->withDelay($options['delay']);
        Accesor::set($job, 'retries', $options['retries']);
        $job->withMaxRetries($options['maxRetries']);
        $job->withPreferredQueueName($options['preferredQueueName']);
    }
}