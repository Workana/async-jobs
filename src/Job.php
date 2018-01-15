<?php
namespace Workana\AsyncJobs;

use InvalidArgumentException;
use Bernard\Message;

/**
 * Abstract job
 *
 * @author Carlos Frutos <charly@workana.com>
 */
abstract class Job implements Message 
{
    const DEFAULT_MAX_RETRIES = 10;

    /**
     * @var int
     */
    protected $delay = 0;

    /**
     * @var int
     */
    protected $retries = 0;

    /**
     * @var int
     */
    protected $maxRetries = self::DEFAULT_MAX_RETRIES;

    /**
     * @var string?
     */
    protected $preferredQueueName;

    /**
     * @var bool
     */
    protected $shouldRetry = true;

    /**
     * {@inheritdoc}
     */
    public final function getName()
    {
        $class = get_class($this);

        return current(array_reverse(explode('\\', $class)));
    }

    /**
     * Get delay
     *
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * With delay (in seconds)
     *
     * @param int $delay Delay
     *
     * @return void
     */
    public function withDelay($delay = 0)
    {
        $this->delay = (int) $delay;
    }

    /**
     * Increment retries, step one
     *
     * @return void
     */
    public function incrementRetries()
    {
        $nextRetry = $this->retries + 1;

        if ($nextRetry > $this->maxRetries) {
            throw new InvalidArgumentException('Max retries exceeded');
        }

        $this->retries = $nextRetry;
    }

    /**
     * Total retries
     *
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * Are retries exhausted?
     *
     * @return bool
     */
    public function areRetriesExhausted()
    {
        return ($this->retries === $this->maxRetries);
    }

    /**
     * Max retries
     *
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * @param int $maxRetries
     *
     * @return int
     */
    public function withMaxRetries($maxRetries)
    {
        $this->maxRetries = (int) $maxRetries;
    }

    public function getPreferredQueueName()
    {
        return $this->preferredQueueName;
    }

    public function hasPreferredQueue()
    {
        return !empty($this->preferredQueueName);
    }

    public function withPreferredQueueName($preferredQueueName)
    {
        $this->preferredQueueName = $preferredQueueName ?(string) $preferredQueueName : null;
    }

    public function dontRetry()
    {
        $this->shouldRetry = false;
    }

    public function shouldRetry()
    {
        return $this->shouldRetry;
    }
}