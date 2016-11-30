<?php
namespace Workana\AsyncJobs\Doctrine;

/**
 * Queueable entity contract
 *
 * @author Carlos Frutos <charly@workana.com>
 */
interface QueueableEntityInterface
{
    /**
     * Get primary key
     *
     * @return mixed
     */
    public function getId();
}