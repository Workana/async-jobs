<?php
namespace Workana\AsyncJobs\Formatter;

interface FormatterInterface
{
    /**
     * Format an object
     *
     * @param mixed $target
     *
     * @return string
     */
    public function format($target);

    /**
     * Can this formatter format a specified object?
     *
     * @param mixed $target
     *
     * @return bool
     */
    public function canFormat($target);
}