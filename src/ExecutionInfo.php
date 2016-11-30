<?php
namespace Workana\AsyncJobs;

use Bernard\Queue;

class ExecutionInfo
{
    /**
     * @var array
     */
    protected $data;

    public function __construct(Job $job, Queue $queue, Stopwatch $stopwatch)
    {
        $this->data = [
            'delay' => $job->getDelay(),
            'retries' => $job->getRetries(),
            'maxRetries' => $job->getMaxRetries(),
            'queueName' => (string) $queue,
            'preferredQueue' => $job->getPreferredQueueName(),
            'executionTime' => $stopwatch->elapsed(),
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}