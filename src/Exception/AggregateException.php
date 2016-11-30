<?php
namespace Workana\AsyncJobs\Exception;

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