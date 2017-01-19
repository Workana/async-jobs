<?php
namespace Workana\AsyncJobs\Util;

class Sleeper
{
    /**
     * Sleep (in millisecond)
     *
     * @param int $millisecond
     */
    public function sleep($millisecond)
    {
        usleep($millisecond * 1000);
    }
}