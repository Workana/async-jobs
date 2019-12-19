<?php
namespace Workana\AsyncJobs;

use Bernard\Driver;
use Bernard\Serializer;
use Bernard\QueueFactory\PersistentFactory;
use Psr\Container\ContainerInterface ;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Normalt\Normalizer\AggregateNormalizer;
use Workana\AsyncJobs\Dispatcher\AsyncJobDispatcher;
use Bernard\Normalizer\EnvelopeNormalizer;
use Workana\AsyncJobs\Doctrine\QueueableEntityNormalizer;
use Workana\AsyncJobs\Normalizer\AsyncActionNormalizer;
use Workana\AsyncJobs\Normalizer\ParameterNormalizer;
use Workana\AsyncJobs\Normalizer\ScalarNormalizer;
use Workana\AsyncJobs\Normalizer\SerializableEventNormalizer;

/**
 * Job Manager
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class JobManager
{
    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Bernard\QueueFactory
     */
    private $queueFactory;

    /**
     * @var \Bernard\Router
     */
    private $router;

    /**
     * @var JobDispatcher
     */
    private $jobDispatcher;

    /**
     * Creates a new job manager
     *
     * @param Configuration $config
     * @param ContainerInterface $container
     */
    public function __construct(Configuration $config, ContainerInterface $container)
    {
        $this->driver = $container->get($config->getDriverClass());
        $this->config = $config;
        $this->container = $container;
        $this->eventDispatcher = new EventDispatcher();

        $serializer = $this->createSerializer();
        $this->queueFactory = new PersistentFactory($this->driver, $serializer);

        $this->router = $this->container->get($config->getRouterClass());
        $this->jobDispatcher = $this->createDispatcher();
    }

    /**
     * @return JobDispatcher
     * @throws \Exception
     */
    private function createDispatcher()
    {
        if ($this->config->isSync()) {
            throw new \Exception('Not implemented yet: sync mode');
        } else {
            return new AsyncJobDispatcher($this);
        }
    }

    /**
     * Creates serializer based on declared normalizers
     *
     * @return Serializer
     */
    private function createSerializer()
    {
        $normalizerClasses = array_merge([
            EnvelopeNormalizer::class,
            SerializableEventNormalizer::class,
            AsyncActionNormalizer::class,
            ParameterNormalizer::class,
            ScalarNormalizer::class
        ], $this->config->getNormalizerClasses());

        if ($this->config->isUsingDoctrine()) {
            $normalizerClasses[] = QueueableEntityNormalizer::class;
        }

        $normalizers = array_map([$this->container, 'get'], $normalizerClasses);
        $aggregate = new AggregateNormalizer($normalizers);

        return new Serializer($aggregate);
    }

    /**
     * Creates a new worker builder
     *
     * @return WorkerBuilder
     */
    public function createWorkerBuilder()
    {
        return new WorkerBuilder($this);
    }

    /**
     * Dispatch a job
     */
    public function dispatch(Job $job)
    {
        $this->jobDispatcher->dispatch($job);
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Get driver
     *
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return \Bernard\QueueFactory
     */
    public function getQueueFactory()
    {
        return $this->queueFactory;
    }

    /**
     * @return \Bernard\Router
     */
    public function getRouter()
    {
        return $this->router;
    }
}