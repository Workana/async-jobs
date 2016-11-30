<?php
namespace Workana\AsyncJobs\Router;

use Bernard\Router\SimpleRouter;
use Workana\AsyncJobs\Executor\AsyncActionExecutor;

/**
 * Default execution router
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class DefaultRouter extends SimpleRouter
{
    /**
     * Creates a new instance
     *
     * @param AsyncActionExecutor $asyncActionExecutor
     */
    public function __construct(
        AsyncActionExecutor $asyncActionExecutor
    ) {
        $this->add('AsyncAction', $asyncActionExecutor);
        $this->add('AsyncEvent', $asyncActionExecutor);
    }
}