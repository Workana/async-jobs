<?php
namespace Workana\AsyncJobs\Formatter;

use Workana\AsyncJobs\Job;

class DefaultJobFormatter implements JobFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(Job $job)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function canFormat(Job $job)
    {
        //It always can format a job
        return true;
    }
}