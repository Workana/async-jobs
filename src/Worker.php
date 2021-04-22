<?php
namespace Workana\AsyncJobs;

use Throwable;
use Exception;
use Bernard\Envelope;
use Bernard\Queue;
use Bernard\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Workana\AsyncJobs\Event\SuccessfulExecutionEvent;
use Workana\AsyncJobs\Process\ProcessManager;
use Workana\AsyncJobs\Event\AfterExecutionEvent;
use Workana\AsyncJobs\Event\BeforeExecutionEvent;
use Workana\AsyncJobs\Event\RejectedExecutionEvent;
use Workana\AsyncJobs\Event\WorkerShutdownEvent;
use Workana\AsyncJobs\Util\Sleeper;
use Workana\AsyncJobs\Retry\RetryStrategy;

/**
 * Worker class
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class Worker
{
    /**
     * @var ProcessManager
     */
    protected $pm;

    /**
     * @var Stopwatch
     */
    protected $stopwatch;

    /**
     * @var Sleeper
     */
    protected $sleeper;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var RetryStrategy
     */
    protected $retryStrategy;

    /**
     * @var int
     */
    protected $shutdownSignal = null;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Create a new Worker
     *
     * @param Queue $queue
     * @param Router $router
     * @param EventDispatcherInterface $eventDispatcher
     * @param ProcessManager $processManager
     * @param RetryStrategy $retryStrategy
     */
    public function __construct(
        Queue $queue,
        Router $router,
        EventDispatcherInterface $eventDispatcher,
        ProcessManager $processManager,
        RetryStrategy $retryStrategy
    ) {
        $this->queue = $queue;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->pm = $processManager;

        $this->stopwatch = new Stopwatch();
        $this->sleeper = new Sleeper();

        $this->retryStrategy = $retryStrategy;

        $this->bindSignals();
    }

    /**
     * Bind signal handlers
     *
     * @return void
     */
    protected function bindSignals()
    {
        $this->pm->signals()->registerHandler(SIGTERM, [$this, 'shutdown']);
        $this->pm->signals()->registerHandler(SIGINT, [$this, 'shutdown']);
        $this->pm->signals()->registerHandler(SIGQUIT, [$this, 'shutdown']);
    }

    /**
     * Shutdown worker
     *
     * @return void
     */
    public function shutdown($signal = SIGTERM)
    {
        $this->shutdownSignal = $signal;

        $this->eventDispatcher->dispatch(AsyncJobsEvents::WORKER_SHUTDOWN, new WorkerShutdownEvent());
    }

    /**
     * Run worker
     *
     * @return void
     */
    public function run()
    {
        while ($this->shouldContinue()) {
            $envelope = $this->queue->dequeue();

            if ($envelope) {
                $this->invoke($envelope);
            } else {
                $this->sleeper->sleep(1);
            }
        }
    }

    /**
     * @return bool
     */
    protected function shouldContinue()
    {
        $this->pm->dispatch();
        return empty($this->shutdownSignal);
    }

    /**
     * Invoke job execution
     *
     * @param Envelope $envelope
     *
     * @return void
     */
    public function invoke(Envelope $envelope)
    {
        $this->eventDispatcher->dispatch(AsyncJobsEvents::BEFORE_EXECUTION, new BeforeExecutionEvent());

        try {
            $this->stopwatch->start();

            call_user_func($this->router->route($envelope)->receive($envelope->getMessage()));

            $this->queue->acknowledge($envelope);

            $info = new ExecutionInfo($envelope->getMessage(), $this->queue, $this->stopwatch);
            $this->eventDispatcher->dispatch(AsyncJobsEvents::SUCCESSFUL_EXECUTION, new SuccessfulExecutionEvent($envelope, $this, $info));
        } catch (Throwable $t) {
            $this->handleRejected($envelope, $t);
        } catch (Exception $e) {
            $this->handleRejected($envelope, $e);
        }

        $this->eventDispatcher->dispatch(AsyncJobsEvents::AFTER_EXECUTION, new AfterExecutionEvent());
    }

    /**
     * Get Process Manager
     *
     * @return ProcessManager
     */
    public function getProcessManager()
    {
        return $this->pm;
    }

    /**
     * Handle rejected job
     *
     * @param Envelope $envelope Envelope
     * @param Throwable|Exception $error Error
     */
    protected function handleRejected(Envelope $envelope, $error)
    {
        $info = new ExecutionInfo($envelope->getMessage(), $this->queue, $this->stopwatch);
        $this->eventDispatcher->dispatch(AsyncJobsEvents::REJECTED_EXECUTION, new RejectedExecutionEvent(
            $envelope,
            $error,
            $this,
            $info
        ));

        $this->queue->acknowledge($envelope);
        $this->retryStrategy->handleRetry($envelope, $error);
    }
}