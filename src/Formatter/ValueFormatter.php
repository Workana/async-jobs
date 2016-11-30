<?php
namespace Workana\AsyncJobs\Formatter;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class ValueFormatter implements FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format($target)
    {
        if (is_null($target)) {
            return '(null)';
        } elseif (is_array($target)) {
            return '(Array) {...}';
        } elseif (is_scalar($target)) {
            $type = gettype($target);

            if (is_bool($target)) {
                $value = $target ? 'true' : 'false';
            } else {
                $value = (string) $target;
            }

            return strtr('(:type) :value', [
                ':type' => $type,
                ':value' => $value,
            ]);
        } else {
            return strtr('(:class) {...}', [':class' => get_class($target)]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canFormat($target)
    {
        /**
         * Can format always
         */
        return true;
    }
}