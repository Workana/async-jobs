<?php
namespace Workana\AsyncJobs;

/**
 * Async Jobs Events
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AsyncJobsEvents
{
    const BEFORE_EXECUTION = 'async.beforeExecution';
    const AFTER_EXECUTION = 'async.afterExecution';
    const SUCCESSFUL_EXECUTION = 'async.successfulExecution';
    const REJECTED_EXECUTION = 'async.rejectedExecution';
    const WORKER_SHUTDOWN = 'async.workerShutdown';
    const PING = 'async.ping';
}