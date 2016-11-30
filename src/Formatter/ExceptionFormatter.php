<?php
namespace Workana\AsyncJobs\Formatter;

use Exception;

/**
 * Format an exception
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class ExceptionFormatter extends AggregateFormatterAware
{
    /**
     * {@inheritdoc}
     */
    public function format($target)
    {
        return strtr('":message" on :file line :line (:type)', [
            ':message' => $target->getMessage(),
            ':file' => $target->getFile(),
            ':line' => $target->getLine(),
            ':type' => get_class($target),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function canFormat($target)
    {
        return ($target instanceof Exception);
    }
}