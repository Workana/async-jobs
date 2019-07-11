<?php
namespace Workana\AsyncJobs\Executor;

use Workana\AsyncJobs\AsyncAction;
use Interop\Container\ContainerInterface;

/**
 * Async action executor
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AsyncActionExecutor
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Creates a new instance
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Executes the specified action
     *
     * @param AsyncAction $action
     *
     * @return void
     */
    public function __invoke(AsyncAction $action)
    {
        $target = $this->container->get($action->getClass());

        call_user_func_array([$target, $action->getMethod()], $action->getParameterValues());
    }
}