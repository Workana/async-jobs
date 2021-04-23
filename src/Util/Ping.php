<?php
namespace Workana\AsyncJobs\Util;

use Workana\AsyncJobs\JobManager;
use Workana\AsyncJobs\Event\PingEvent;
use Workana\AsyncJobs\AsyncJobsEvents;

/**
 * Produces a ping
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class Ping
{
    /**
     * @var JobManager
     */
    protected $jm;

    public function __construct(JobManager $jm)
    {
        $this->jm = $jm;
    }

    /**
     * @return void
     */
    public function ping()
    {
        $this->jm->getEventDispatcher()->dispatch(new PingEvent(), AsyncJobsEvents::PING);
    }
}
