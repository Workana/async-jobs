<?php
namespace Workana\AsyncJobs\Event;

use Symfony\Component\EventDispatcher\Event;
use Workana\AsyncJobs\ExecutionInfo;
use Workana\AsyncJobs\Job;
use Bernard\Envelope;
use Workana\AsyncJobs\Worker;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class RejectedExecutionEvent extends Event
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var Envelope
     */
    protected $envelope;

    /**
     * @var Exception|Throwable
     */
    protected $error;

    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var ExecutionInfo
     */
    protected $info;

    /**
     * @param Exception|Throwable $error
     */
    public function __construct(Envelope $envelope, $error, Worker $worker, ExecutionInfo $info)
    {
        $this->envelope = $envelope;
        $this->job = $envelope->getMessage();
        $this->error = $error;
        $this->worker = $worker;
        $this->info = $info;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return Envelope
     */
    public function getEnvelope()
    {
        return $this->envelope;
    }

    /**
     * @return Exception|Throwable
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return Worker
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @return ExecutionInfo
     */
    public function getInfo()
    {
        return $this->info;
    }
}