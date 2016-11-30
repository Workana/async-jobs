<?php
namespace Workana\AsyncJobs\EventDispatching;

use Exception;
use ReflectionFunction;

/**
 * Aggregate child of event exception
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AggregateChildEventException extends Exception
{
    /**
     * @var AggregateRootEventException
     */
    protected $parent;

    /**
     * @var Callable
     */
    protected $listener;

    /**
     * @var string
     */
    protected $listenerName;

    /**
     * @var Throwable|Exception
     */
    protected $wrappedError;

    /**
     * @var AggregateRootEventException $parent
     * @var Callable $listener
     * @var Exception|Throwable $wrappedError
     */
    public function __construct(
        AggregateRootEventException $parent,
        Callable $listener,
        $wrappedError
    ) {
        $this->parent = $parent;
        $this->listener = $listener;
        $this->listenerName = $this->calculateListenerName($listener);
        $this->wrappedError = $wrappedError;

        parent::__construct(strtr('Failed event :eventName on listener :listenerName with message: :errorMessage', [
            ':eventName' => $this->parent->getEventName(),
            ':listenerName' => $this->listenerName,
            ':errorMessage' => $this->wrappedError->getMessage(),
        ]));
    }

    /**
     * Calculate listener name
     *
     * @param Callable $listener
     *
     * @return string
     */
    private function calculateListenerName(Callable $listener)
    {
        if (is_array($listener)) {
            $class = is_object($listener[0]) ? get_class($listener[0]) : (string) $listener[0];

            return implode('::', [$class, $listener[1]]);
        } elseif ($listener instanceof Closure) {
            $reflection = new ReflectionFunction($callable);

            return "[closure]#{$reflection->getFileName()}:{$reflection->getStartLine()}";
        } elseif (is_string($listener)) {
            return $listener;
        } elseif (is_object($listener)) {
            $class = get_class($listener);

            return "[invokable]#{class}";
        }
    }

    public function getEventName()
    {
        return $this->parent->getEventName();
    }

    public function getEvent()
    {
        return $this->parent->getEvent();
    }

    public function getListener()
    {
        return $this->listener;
    }

    public function getListenerName()
    {
        return $this->listenerName;
    }

    public function getWrappedError()
    {
        return $this->wrappedError;
    }
}