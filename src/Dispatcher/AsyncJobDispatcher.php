<?php
namespace Workana\AsyncJobs\Dispatcher;

use Bernard\Envelope;
use Workana\AsyncJobs\Job;
use Workana\AsyncJobs\JobManager;
use Workana\AsyncJobs\JobDispatcher;

/**
 * Job dispatcher on async mode
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AsyncJobDispatcher implements JobDispatcher
{
    /**
     * @var JobManager
     */
    protected $jm;

    /**
     * @var QueueDecider
     */
    protected $queueDecider;

    /**
     * Creates a new instance
     *
     * @param JobManager $jm
     */
    public function __construct(JobManager $jm)
    {
        $this->jm = $jm;
        $this->queueDecider = new QueueDecider(
            $jm->getQueueFactory(),
            $jm->getConfiguration()->getDefaultQueueName(),
            $jm->getConfiguration()->getDispatchingRules()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(Job $job)
    {
        $queue = $this->queueDecider->decide($job);
        
        $envelope = new Envelope($job, $job->getDelay());

        $queue->enqueue($envelope);
    }
}