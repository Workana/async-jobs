<?php
namespace Workana\AsyncJobs\EventDispatching;

use Exception;
use Throwable;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

/**
 * Failover event dispatcher
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class FailoverEventDispatcher extends EventDispatcher
{
    /**
     * {@inheritDoc}
     *
     * @throws AggregateRootEventException
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        /**
         * Array of arrays of type [$callable, $reason]
         */
        $failedListeners = [];

        foreach ($listeners as $listener) {
            try {
                call_user_func($listener, $event, $eventName, $this);
            } catch (Throwable $t) {
                $failedListeners[] = [$listener, $t];
            } catch (Exception $e) {
                $failedListeners[] = [$listener, $e];
            }
            
            if ($event->isPropagationStopped()) {
                break;
            }
        }

        if (!empty($failedListeners)) {
            throw new AggregateRootEventException($eventName, $event, $failedListeners);
        }
    }
}