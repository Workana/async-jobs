<?php
namespace Workana\AsyncJobs\Tests\Retry;

use Assert\InvalidArgumentException;
use Bernard\Envelope;
use Bernard\Message;
use Exception;
use Workana\AsyncJobs\AsyncAction;
use Workana\AsyncJobs\JobManager;
use Workana\AsyncJobs\Retry\BinaryExponentialBackoff;
use Workana\AsyncJobs\Tests\Test;
use Mockery as m;

class BinaryExponentialBackoffTest extends Test
{
    /**
     * @var JobManager
     */
    protected $mockedJobManager;

    /**
     * @var BinaryExponentialBackoff
     */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockedJobManager = m::mock(JobManager::class);
        $this->sut = new BinaryExponentialBackoff();
        $this->sut->setJobManager($this->mockedJobManager);

        $this->mockedJobManager->shouldReceive('dispatch');
    }

    public function testHandleRetryRetriesExhausted()
    {
        $job = new AsyncAction('Foo', 'Bar');

        $job->withMaxRetries(1);

        $envelope = new Envelope($job);

        $this->sut->handleRetry($envelope, m::mock(Exception::class));

        $this->assertEquals(1, $job->getRetries());
        $this->assertTrue($job->areRetriesExhausted());
    }

    public function testHandleRetryInspectSeconds()
    {
        $mockedError = m::mock(Exception::class);

        $job = new AsyncAction('Foo', 'Bar');
        $job->withMaxRetries(10);

        $this->mockedJobManager->shouldReceive('dispatch')
                ->with($job)
                ->times(10)
                ->byDefault();

        $retriesLog = [];
        foreach (range(1, 10) as $retry) {
            $envelope = new Envelope($job);
            $this->sut->handleRetry($envelope, $mockedError);
            $this->assertEquals($retry, $job->getRetries());
            $retriesLog[$job->getRetries()] = $job->getDelay();
        }

        $this->assertEquals([
            1 => 2,
            2 => 4,
            3 => 8,
            4 => 16,
            5 => 32,
            6 => 64,
            7 => 128,
            8 => 256,
            9 => 512,
            10 => 1024
        ], $retriesLog);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Envelope message must have a valid job instance
     */
    public function testHandleRetryNotJobInstance()
    {
        $envelope = new Envelope(m::mock(Message::class));
        $this->sut->handleRetry($envelope, m::mock(Exception::class));
    }
}