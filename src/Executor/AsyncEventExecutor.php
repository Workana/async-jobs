<?php
namespace Workana\AsyncJobs\Executor;

use Workana\AsyncJobs\AsyncEvent;
use Psr\Container\ContainerInterface ;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Executes an async event
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AsyncEventExecutor
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Invokes the executor
     *
     * @var AsyncEvent $asyncEvent
     *
     * @return void
     */
    public function __invoke(AsyncEvent $asyncEvent)
    {
        $this->eventDispatcher->dispatch($asyncEvent->getEventName(), $asyncEvent->getEvent());
    }
}