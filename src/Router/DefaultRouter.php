<?php
namespace Workana\AsyncJobs\Router;

use Bernard\Router\ReceiverMapRouter;
use Workana\AsyncJobs\Executor\AsyncActionExecutor;

/**
 * Default execution router
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class DefaultRouter extends ReceiverMapRouter
{
    /**
     * Creates a new instance
     *
     * @param AsyncActionExecutor $asyncActionExecutor
     */
    public function __construct(
        AsyncActionExecutor $asyncActionExecutor
    ) {
        parent::__construct([
            'AsyncAction' => $asyncActionExecutor,
            'AsyncEvent' => $asyncActionExecutor
        ]);
    }
}