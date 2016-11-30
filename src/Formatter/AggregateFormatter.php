<?php
/**
 * Created by PhpStorm.
 * User: cronopio
 * Date: 27/11/16
 * Time: 19:42
 */

namespace Workana\AsyncJobs\Formatter;
use Workana\AsyncJobs\Util\NormalPriorityQueue;

/**
 * Aggregate of formatters
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AggregateFormatter
{
    /**
     * @var NormalPriorityQueue
     */
    protected $formatters;

    public function __construct()
    {
        $this->formatters = new NormalPriorityQueue();
    }

    /**
     * Add formatter
     *
     * @param FormatterInterface $formatter
     * @param int $priority
     *
     * @return void
     */
    public function addFormatter(FormatterInterface $formatter, $priority = 0)
    {
        if ($formatter instanceof AggregateFormatterAware) {
            $formatter->setAggregateFormatter($this);
        }

        $this->formatters->insert($formatter, (int) $priority);
    }

    /**
     * @return FormatterInterface|null
     */
    protected function getFormatter($target)
    {
        foreach (clone $this->formatters as $formatter) {
            if ($formatter->canFormat($target)) {
                return $formatter;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canFormat($target)
    {
        return (bool) $this->getFormatter($target);
    }

    /**
     * {@inheritdoc}
     */
    public function format($target)
    {
        return $this->getFormatter($target)->format($target);
    }
}