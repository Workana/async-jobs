<?php
namespace Workana\AsyncJobs\Process;

use Ko\ProcessManager as PM;

/**
 * Internal Process Manager
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class ProcessManager extends PM
{
    /**
     * Get signal handler
     *
     * @return SignalHandler
     */
    public function signals()
    {
        return $this->signalHandler;
    }
} 