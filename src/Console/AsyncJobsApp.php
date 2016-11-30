<?php
namespace Workana\AsyncJobs\Console;

use Symfony\Component\Console\Application;

/**
 * Async jobs console application
 *
 * @version 0.9
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class AsyncJobsApp extends Application
{
    /**
     * Creates a new app
     */
    public function __construct(
        ConsumeCommand $consumeCommand,
        PingCommand $pingCommand
    ) {
        parent::__construct('Workana AsyncJobs', '0.9');

        $this->add($consumeCommand);
        $this->add($pingCommand);
    }
}

