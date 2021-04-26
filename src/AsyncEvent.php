<?php
namespace Workana\AsyncJobs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class AsyncEvent extends AsyncAction
{
    /**
     * @var string $eventName
     * @var SerializableEvent $event
     */
    public function __construct($eventName, SerializableEvent $event)
    {
        parent::__construct(EventDispatcherInterface::class, 'dispatch', [$event, $eventName]);
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->parameters[1]->getValue();
    }

    /**
     * @return SerializableEvent
     */
    public function getEvent()
    {
        return $this->parameters[0]->getValue();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getEventName();
    }
}