<?php
namespace Workana\AsyncJobs\Util;

use SplPriorityQueue;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class NormalPriorityQueue extends SplPriorityQueue
{
    public function compare($priority1, $priority2)
    {
        if ($priority1 === $priority2) {
            return 0;
        }
        return ($priority1 < $priority2) ? -1 : 1;
    }
}