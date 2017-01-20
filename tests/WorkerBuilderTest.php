<?php
namespace Workana\AsyncJobs\Tests;

use Assert\InvalidArgumentException;
use Bernard\Envelope;
use Bernard\Message;
use Bernard\Queue;
use Exception;
use Workana\AsyncJobs\AsyncAction;
use Workana\AsyncJobs\JobManager;
use Workana\AsyncJobs\Retry\BinaryExponentialBackoff;

use Mockery as m;
use Workana\AsyncJobs\WorkerBuilder;

class WorkerBuilderTest extends Test
{
    /**
     * @var JobManager
     */
    protected $mockedJobManager;

    /**
     * @var WorkerBuilder
     */
    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->mockedJobManager = m::mock(JobManager::class);

        $this->sut = new WorkerBuilder($this->mockedJobManager);

        $this->mockedJobManager->shouldReceive('dispatch');
    }

    public function testUsingMultipleQueues()
    {
        $queueNames = ['q1', 'q2'];

        $this->mockedJobManager->shouldReceive('getRetryStrategy')
            ->once()
            ->andReturn(m::mock(BinaryExponentialBackoff::class));

        foreach($queueNames as $queueName) {
            $this->mockedJobManager->shouldReceive('getQueueFactory->create')
                ->once()
                ->andReturn(new Queue\InMemoryQueue($queueName));
        }
        $this->sut->usingMultipleQueues($queueNames);
        var_dump($this->sut->build());
    }


}