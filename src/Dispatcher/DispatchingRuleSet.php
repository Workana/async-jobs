<?php
namespace Workana\AsyncJobs\Dispatcher;

use SplPriorityQueue;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class DispatchingRuleSet extends SplPriorityQueue
{
    /**
     * {@inheritDoc}
     */
    public function compare($priority1, $priority2) 
    { 
        if ($priority1 === $priority2) return 0; 
        return $priority1 < $priority2 ? -1 : 1; 
    }
}