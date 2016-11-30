<?php
namespace Workana\AsyncJobs\Event;

use Bernard\Envelope;
use Symfony\Component\EventDispatcher\Event;
use Workana\AsyncJobs\ExecutionInfo;
use Workana\AsyncJobs\Worker;

/**
 * Occurs after successful execution
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class SuccessfulExecutionEvent extends Event
{
    /**
     * @var Envelope
     */
    protected $envelope;

    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var ExecutionInfo
     */
    protected $info;

    /**
     * SuccessfulExecutionEvent constructor.
     * @param Envelope $envelope
     * @param Worker $worker
     */
    public function __construct(Envelope $envelope, Worker $worker, ExecutionInfo $info)
    {
        $this->envelope = $envelope;
        $this->worker = $worker;
        $this->info = $info;
    }

    /**
     * @return Envelope
     */
    public function getEnvelope()
    {
        return $this->envelope;
    }

    /**
     * @return \Workana\AsyncJobs\Job
     */
    public function getJob()
    {
        return $this->envelope->getMessage();
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