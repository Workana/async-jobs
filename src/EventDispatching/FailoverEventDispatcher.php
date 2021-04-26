<?php

namespace Workana\AsyncJobs\EventDispatching;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

/**
 * Failover event dispatcher
 */
class FailoverEventDispatcher extends EventDispatcher
{
    /**
     * {@inheritdoc}
     */
    protected function callListeners(iterable $listeners, string $eventName, $event)
    {
        /**
         * Array of arrays of type [$callable, $reason]
         */
        $failedListeners = [];

        foreach ($listeners as $listener) {
            try {
                /** @var Event $event */
                $this->executeListener($listener, $event, $eventName);
            } catch (Throwable $t) {
                $failedListeners[] = [$listener, $t];
            }

            if ($event->isPropagationStopped()) {
                break;
            }
        }

        if (!empty($failedListeners)) {
            throw new AggregateRootEventException($eventName, $event, $failedListeners, (array)$listeners);
        }
    }

    protected function executeListener(callable $listener, Event $event, string $eventName)
    {
        call_user_func($listener, $event, $eventName, $this);
    }
}
