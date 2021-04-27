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
        //Events produced with previous code version should have inverted parameters, so we need some logic
        //to detect how it was produced and adapt, since there will be delayed jobs.
        $eventName = $this->parameters[1]->getValue();
        return (is_string($eventName)) ? $eventName :  $this->parameters[0]->getValue();
    }

    /**
     * @return SerializableEvent
     */
    public function getEvent()
    {
        //Events produced with previous code version should have inverted parameters, so we need some logic
        //to detect how it was produced and adapt, since there will be delayed jobs.
        $eventData = $this->parameters[0]->getValue();
        return ($eventData instanceof SerializableEvent) ? $eventData : $this->parameters[1]->getValue();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getEventName();
    }
}