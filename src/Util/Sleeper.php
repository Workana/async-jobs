<?php
namespace Workana\AsyncJobs\Util;

class Sleeper
{
    /**
     * Sleep (in miliseconds)
     *
     * @param int $miliseconds
     */
    public function sleep($miliseconds)
    {
        usleep($miliseconds * 1000);
    }
}