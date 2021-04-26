<?php
namespace Workana\AsyncJobs\EventDispatching;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

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
                $this->executeListener($listener, $event, $eventName);

            } catch (Throwable $t) {
                $failedListeners[] = [$listener, $t];
            }
            
            if ($event->isPropagationStopped()) {
                break;
            }
        }

        if (!empty($failedListeners)) {
            throw new AggregateRootEventException($eventName, $event, $failedListeners, $listeners);
        }
    }

    /**
     * @param callable $listener
     * @param Event $event
     * @param string $eventName
     */
    protected function executeListener(callable $listener,Event $event,string $eventName)
    {
        call_user_func($listener, $event, $eventName, $this);
    }
}