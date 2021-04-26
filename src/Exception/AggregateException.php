<?php
namespace Workana\AsyncJobs\Exception;

use Exception;

/**
 * Aggregate exception
 *
 * @author Carlos Frutos <charly@workana.com>
 */
interface AggregateException
{
    /**
     * @return Exception[]
     */
    public function getChildren();
}