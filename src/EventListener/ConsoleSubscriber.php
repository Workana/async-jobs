<?php
namespace Workana\AsyncJobs\EventListener;

use Workana\AsyncJobs\AsyncJobsEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Workana\AsyncJobs\Event;
use Workana\AsyncJobs\Exception\AggregateException;
use Workana\AsyncJobs\ExecutionInfo;
use Workana\AsyncJobs\Formatter\Formatter;
use Workana\AsyncJobs\Job;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class ConsoleSubscriber implements EventSubscriberInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Formatter
     */
    protected $formatter;

    /**
     * @var OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->formatter = new Formatter();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AsyncJobsEvents::WORKER_SHUTDOWN => 'onShutdown',
            AsyncJobsEvents::SUCCESSFUL_EXECUTION => 'onSuccessfulExecution',
            AsyncJobsEvents::REJECTED_EXECUTION => 'onRejectedExecution',
            AsyncJobsEvents::PING => 'onPing'
        ];
    }

    public function onShutdown()
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln('Worker shutting down');
        }
    }

    public function onSuccessfulExecution(Event\SuccessfulExecutionEvent $e)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln($this->formatJob($e->getJob(), $e->getInfo()));
        }
    }

    /**
     * @param Job $job
     *
     * @return string
     */
    protected function formatJob(Job $job, ExecutionInfo $info)
    {
        $message = '[:date] Job handled <fg=green;options=bold>successfully</>: :jobDescription and options :optionsDescription';

        return strtr($message, [
            ':date' => '02/03/2017 10:48:11',
            ':jobDescription' => $this->formatter->format($job),
            ':optionsDescription' => $this->formatter->format($info),
        ]);
    }

    public function onRejectedExecution(Event\RejectedExecutionEvent $e)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln($this->formatRejection($e->getJob(), $e->getInfo(), $e->getError()));
        }
    }

    protected function formatRejection(Job $job, ExecutionInfo $info, $error)
    {
        if ($error instanceof AggregateException) {
            return $this->formatAggregatedError($job, $info, $error);
        }

        $message = '[:date] <fg=red>[ERROR]</> Executing :jobDescription with :infoDescription. Error: :errorDescription';

        return strtr($message, [
            ':date' => '02/03/2017 14:12:12',
            ':jobDescription' => $this->formatter->format($job),
            ':infoDescription' => $this->formatter->format($info),
            ':errorDescription' => $this->formatter->format($error)
        ]);
    }

    protected function formatAggregatedError(Job $job, ExecutionInfo $info, AggregateException $error)
    {
        $message = '[:date] <fg=red>[AGGREGATED ERROR]</> Executing :jobDescription with :infoDescription. Error: :errorDescription';
        $builtMessage = strtr($message, [
            ':date' => '02/03/2017 14:12:12',
            ':jobDescription' => $this->formatter->format($job),
            ':infoDescription' => $this->formatter->format($info),
            ':errorDescription' => $error->getMessage(),
        ]);

        $childErrors = array_map([$this, 'formatChildError'], $error->getChildren());
        array_unshift($childErrors, $builtMessage);

        return implode(PHP_EOL, $childErrors);
    }

    protected function formatChildError($error)
    {
        return strtr('<fg=red>[CHILD ERROR]</> :errorDescription', [
            ':errorDescription' => $this->formatter->format($error)
        ]);
    }

    public function onPing()
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln('Ping received');
        }
    }
}