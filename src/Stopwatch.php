<?php
namespace Workana\AsyncJobs;

/**
 * Stopwatch
 */
class Stopwatch
{
    /**
     * @var float
     */
    private $startTime = null;

    /**
     * Start (or restart)
     *
     * @return void
     */
    public function start()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Get elapsed time (in seconds)
     *
     * @return float
     */
    public function elapsed()
    {
        return microtime(true) - $this->startTime;
    }
}