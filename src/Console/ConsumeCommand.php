<?php
namespace Workana\AsyncJobs\Console;

use Workana\AsyncJobs\JobManager;
use Workana\AsyncJobs\EventListener\ConsoleSubscriber;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for job consuming
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class ConsumeCommand extends Command
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
        parent::__construct('consume');

        $this->jm = $jm;
    }

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setDescription('Consume queues')
             ->addOption(
                'queues',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma separated names of one or more queues that will be consumed.'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->jm->getEventDispatcher()->addSubscriber(new ConsoleSubscriber($output));

        $queues = array_map('trim', explode(',', $input->getOption('queues')));

        $worker = $this->jm->createWorkerBuilder()->usingMultipleQueues($queues)->build();
        $worker->run();
    }
}