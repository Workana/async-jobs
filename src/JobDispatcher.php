<?php
namespace Workana\AsyncJobs;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
interface JobDispatcher
{
    /**
     * Dispatch a job
     *
     * @param Job $job
     */
    public function dispatch(Job $job);
}