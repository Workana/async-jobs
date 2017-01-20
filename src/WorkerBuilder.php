<?php
namespace Workana\AsyncJobs;

use Bernard\Queue\RoundRobinQueue;
use Workana\AsyncJobs\Process\ProcessManager;

/**
 * Worker builder
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class WorkerBuilder
{
    /**
     * @var JobManager
     */
    protected $jm;

    /**
     * Creates a new Worker builder
     *
     * @param JobManager $jm
     */
    public function __construct(JobManager $jm)
    {
        $this->jm = $jm;
    }

    /**
     * @var string[]
     */
    protected $queueNames = [];

    /**
     * Using queue
     *
     * @param string $queueName
     *
     * @return self
     */
    public function usingQueue($queueName)
    {
        $this->queueNames[] = (string) $queueName;

        return $this;
    }

    /**
     * Using multiple queues
     *
     * @param string[] $queueNames
     *
     * @return self
     */
    public function usingMultipleQueues(array $queueNames)
    {
        $this->queueNames = array_merge(array_filter($queueNames), $this->queueNames);

        return $this;
    }


    /**
     * Build worker
     *
     * @return Worker
     */
    public function build()
    {
        $queue = $this->getQueue();
        $retryStrategy = $this->getRetryStrategy();

        return new Worker(
            $queue,
            $this->jm->getRouter(),
            $this->jm->getEventDispatcher(),
            new ProcessManager(),
            $retryStrategy
        );
    }

    /**
     * @return \Bernard\Queue
     */
    protected function getQueue()
    {
        if (!empty($this->queueNames)) {
            $queueNames = $this->queueNames;
        } else {
            $queueNames = $this->jm->getDriver()->listQueues();
        }

        $qtyQueues = count($queueNames);

        if ($qtyQueues === 1) {
            $queue = $this->jm->getQueueFactory()->create(reset($queueNames));
        } else {
            $queueSet = array_map([$this->jm->getQueueFactory(), 'create'], $queueNames);
            $queue = new RoundRobinQueue($queueSet);
        }

        return $queue;
    }

    /**
     * @return Retry\RetryStrategy
     */
    protected function getRetryStrategy()
    {
        $retryStrategy = $this->jm->getContainer()->get($this->jm->getConfiguration()->getRetryStrategyClass());
        $retryStrategy->setJobManager($this->jm);

        return $retryStrategy;
    }
}