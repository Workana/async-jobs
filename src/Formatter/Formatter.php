<?php
namespace Workana\AsyncJobs\Formatter;

/**
 * Root formatter
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class Formatter extends AggregateFormatter
{
    public function __construct()
    {
        parent::__construct();


        $this->addFormatter(new AggregateChildEventExceptionFormatter(), 1);
        $this->addFormatter(new QueueableEntityFormatter(), 1);
        $this->addFormatter(new AsyncEventFormatter(), 1);
        $this->addFormatter(new AsyncActionFormatter(), 0);
        $this->addFormatter(new ExecutionInfoFormatter(), 0);
        $this->addFormatter(new ExceptionFormatter(), 0);
        $this->addFormatter(new ValueFormatter(), -1);
    }
}