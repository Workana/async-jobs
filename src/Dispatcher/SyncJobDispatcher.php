<?php
namespace Workana\AsyncJobs\Dispatcher;

use Bernard\Envelope;
use Workana\AsyncJobs\Job;
use Workana\AsyncJobs\JobDispatcher;
use Workana\AsyncJobs\JobManager;

class SyncJobDispatcher implements JobDispatcher
{
    /**
     * @var JobManager
     */
    private $jm;

    public function __construct(
        JobManager $jm
    ) {
        $this->jm = $jm;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(Job $job)
    {
        $router = $this->jm->getRouter();

        $envelope = new Envelope($job);

        call_user_func($router->map($envelope), $envelope->getMessage());
    }
}
