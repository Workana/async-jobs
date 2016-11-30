<?php
namespace Workana\AsyncJobs\Formatter;

abstract class AggregateFormatterAware implements FormatterInterface
{
    /**
     * @var AggregateFormatter
     */
    protected $aggregate;

    /**
     * @param AggregateFormatter $aggregate
     */
    public function setAggregateFormatter(AggregateFormatter $aggregate)
    {
        $this->aggregate = $aggregate;
    }
}