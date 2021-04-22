<?php
namespace Workana\AsyncJobs;

use Bernard\Router;
use Bernard\Router\ReceiverMapRouter;
use InvalidArgumentException;
use Workana\AsyncJobs\Executor\AsyncActionExecutor;
use Workana\AsyncJobs\Retry\BinaryExponentialBackoff;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class Configuration
{
    /**
     * @var array
     */
    protected $attributes;

    public function __construct(array $options)
    {
        $router = new ReceiverMapRouter(['AsyncAction' => AsyncActionExecutor::class, 'AsyncEvent' => AsyncActionExecutor::class]);

        $this->attributes = $options + [
            'router' => $router,
            'driverClass' => null,
            'retryStrategyClass' => BinaryExponentialBackoff::class,
            'normalizerClasses' => [],
            'defaultQueueName' => 'default',
            'useDoctrine' => false,
            'enableGedmoSoftDeleteBehaviour' => true,
            'sync' => false,
            'dispatchingRules' => [],
        ];

        if (empty($this->attributes['driverClass'])) {
            throw new InvalidArgumentException('Driver class must be specified');
        }
    }

    /**
     * Get router class
     *
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->attributes['router'];
    }

    /**
     * Get driver class
     *
     * @return string
     */
    public function getDriverClass()
    {
        return $this->attributes['driverClass'];
    }

    /**
     * Get retry strategy class
     *
     * @return string
     */
    public function getRetryStrategyClass()
    {
        return $this->attributes['retryStrategyClass'];
    }

    /**
     * Get normalizer classes
     *
     * @return string[]
     */
    public function getNormalizerClasses()
    {
        return $this->attributes['normalizerClasses'];
    }

    /**
     * @return string
     */
    public function getDefaultQueueName()
    {
        return $this->attributes['defaultQueueName'];
    }

    /**
     * Is on sync mode for job dispatching
     *
     * @return bool
     */
    public function isSync()
    {
        return $this->attributes['sync'];
    }

    /**
     * @return bool
     */
    public function isUsingDoctrine()
    {
        return $this->attributes['useDoctrine'];
    }

    /**
     * @return bool
     */
    public function isEnabledGedmoSoftDeleteBehaviour()
    {
        return $this->attributes['enableGedmoSoftDeleteBehaviour'];
    }

    /**
     * Get dispatching rules
     *
     * @return array
     */
    public function getDispatchingRules()
    {
        return $this->attributes['dispatchingRules'];
    }
}