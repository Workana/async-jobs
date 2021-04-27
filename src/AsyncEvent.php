<?php
namespace Workana\AsyncJobs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AsyncEvent extends AsyncAction
{
    public function __construct(string $eventName, SerializableEvent $event)
    {
        parent::__construct(EventDispatcherInterface::class, 'dispatch', [$event, $eventName]);
    }

    public function getEventName(): string
    {
        //Events produced with previous code version should have inverted parameters, so we need some logic
        //to detect how it was produced and adapt, since there will be delayed jobs.
        $expectedEventNameParameter = $this->parameters[1]->getValue();
        $fallbackParameter = $this->parameters[0]->getValue();

        if (is_string($expectedEventNameParameter)) {
            return $expectedEventNameParameter;
        } elseif (is_string($fallbackParameter)) {
            return $fallbackParameter;
        } else {
            throw new \TypeError('Event name can\'t be derived from event: ' . json_encode($this->parameters));
        }
    }

    public function getEvent(): SerializableEvent
    {
        //Events produced with previous code version should have inverted parameters, so we need some logic
        //to detect how it was produced and adapt, since there will be delayed jobs.
        $expectedEventDataParameter = $this->parameters[0]->getValue();
        $fallbackParameter = $this->parameters[1]->getValue();

        if ($expectedEventDataParameter instanceof SerializableEvent) {
            return $expectedEventDataParameter;
        } elseif ($fallbackParameter instanceof SerializableEvent) {
            return $fallbackParameter;
        } else {
            throw new \TypeError('Event data can\'t be derived from event: ' . json_encode($this->parameters));
        }
    }

    public function __toString(): string
    {
        return $this->getEventName();
    }
}