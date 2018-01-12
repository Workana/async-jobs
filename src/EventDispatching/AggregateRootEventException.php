<?php
namespace Workana\AsyncJobs\EventDispatching;

use Exception;
use ProxyManager\Proxy\VirtualProxyInterface;
use Symfony\Component\EventDispatcher\Event;
use Workana\AsyncJobs\Exception\AggregateException;

/**
 * Root of event exception aggregate
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AggregateRootEventException extends Exception implements AggregateException
{
    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @var ChildEventException[]
     */
    protected $children = [];

    /**
     * Creates a new aggregate exception
     * 
     * @param string $eventName
     * @param Event $event
     * @param array $failedListeners Array of arrays in form [$listener, $error]
     */
    public function __construct($eventName, Event $event, array $failedListeners)
    {
        $this->eventName = (string) $eventName;
        $this->event = $event;

        foreach ($failedListeners as $failedListenerData) {
            list($listener, $error) = $failedListenerData;

            //If object is a proxy then resolve it to report it further
            if ($listener[0] instanceof VirtualProxyInterface) {
                $listener[0]->initializeProxy();
                $listener[0] = $listener[0]->getWrappedValueHolderValue();
            }

            $this->children[] = new AggregateChildEventException($this, $listener, $error);
        }

        parent::__construct(strtr('Failed event :eventName on :count listeners', [
            ':eventName' => $this->eventName,
            ':count' => count($this->children)
        ]));
    }

    /**
     * Associated event name
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Associated event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Children exceptions
     *
     * @return AggregateChildEventException
     */
    public function getChildren()
    {
        return $this->children;
    }
}