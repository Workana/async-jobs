<?php
namespace Workana\AsyncJobs\Formatter;

use Workana\AsyncJobs\AsyncEvent;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class AsyncEventFormatter extends AggregateFormatterAware
{
    /**
     * {@inheritdoc}
     */
    public function format($target)
    {
        $event = $target->getEvent();

        $formattedParameters = array_map(function($value) {
            return '    ' . $this->aggregate->format($value);
        }, $event->getAll());

        array_unshift($formattedParameters, '[');
        array_push($formattedParameters, ']');
        $parametersDescription = implode(PHP_EOL, $formattedParameters);

        $eventClass = current(array_reverse(explode('\\', get_class($event))));

        return strtr(':jobName :jobGroup of type :eventClass with parameters :parametersDescription', [
            ':jobName' => $target->getName(),
            ':jobGroup' => (string) $target,
            ':eventClass' => $eventClass,
            ':parametersDescription' => $parametersDescription
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function canFormat($target)
    {
        return ($target instanceof AsyncEvent);
    }
}