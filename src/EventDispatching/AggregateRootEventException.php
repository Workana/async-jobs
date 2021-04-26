<?php
namespace Workana\AsyncJobs\EventDispatching;

use Exception;
use Symfony\Contracts\EventDispatcher\Event;
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
     * @var AggregateChildEventException[]
     */
    protected $children = [];

    /**
     * @var array
     */
    private $eventListeners;

    /**
     * Creates a new aggregate exception
     * 
     * @param string $eventName
     * @param Event $event
     * @param array $failedListeners Array of arrays in form [$listener, $error]
     */
    public function __construct($eventName, Event $event, array $failedListeners, array $eventListeners)
    {
        $this->eventName = (string) $eventName;
        $this->event = $event;
        $this->eventListeners = $eventListeners;

        foreach ($failedListeners as $failedListenerData) {
            list($listener, $error) = $failedListenerData;

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
     * @return AggregateChildEventException[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Event listeners
     *
     * @return array
     */
    public function getEventListeners(): array
    {
        return $this->eventListeners;
    }
}