<?php
namespace Workana\AsyncJobs\Formatter;

use Workana\AsyncJobs\EventDispatching\AggregateChildEventException;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class AggregateChildEventExceptionFormatter extends AggregateFormatterAware
{
    /**
     * {@inheritdoc}
     */
    public function format($target)
    {
        $wrapped = $target->getWrappedError();
        return strtr('Failed event :event on listener :listener with ":message". File :file line :line (:type)', [
            ':event' =>  $target->getEventName(),
            ':listener' => $target->getListenerName(),
            ':message' => $wrapped->getMessage(),
            ':file' => $wrapped->getFile(),
            ':line' => $wrapped->getLine(),
            ':type' => get_class($wrapped),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function canFormat($target)
    {
        return ($target instanceof AggregateChildEventException);
    }
}