<?php
namespace Workana\AsyncJobs\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Workana\AsyncJobs\Util\Ping;
use Workana\AsyncJobs\AsyncAction;
use Workana\AsyncJobs\JobManager;

/**
 * Produces a ping
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class PingCommand extends Command
{
    /**
     * @var JobManager
     */
    protected $jm;

    /**
     * Creates a new Command
     *
     * @param JobManager $jm
     */
    public function __construct(JobManager $jm)
    {
        parent::__construct('ping');

        $this->jm = $jm;
    }

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setDescription('Send a ping')
             ->addOption(
                'queues',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma separated names of one or more queues that will be pinned.'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $queueNames = array_map('trim', explode(',', $input->getOption('queues')));
        $queueNames = !empty($queues) ?: $this->jm->getDriver()->listQueues();

        foreach ($queueNames as $queueName) {
            $action = new AsyncAction(Ping::class, 'ping');
            $action->withPreferredQueueName($queueName);
            $this->jm->dispatch($action);

            $output->writeln('Ping sent to: ' . $queueName);
        }
    }
}