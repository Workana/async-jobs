<?php
namespace Workana\AsyncJobs\Dispatcher;

use Workana\AsyncJobs\Job;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
abstract class DispatchingRule
{
    const PRIORITY_VERY_LOW = -10;
    const PRIORITY_LOW = -5;
    const PRIORITY_NORMAL = 0;
    const PRIORITY_MEDIUM = 5;
    const PRIORITY_HIGH = 10;

    /**
     * @var int
     */
    protected $priority = self::PRIORITY_NORMAL;

    /**
     * Decides in what queue a job should be dispatched
     *
     * @param Job $job
     *
     * @return string Queue name, or null if It doesn't decide
     */
    public abstract function __invoke(Job $job);

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}