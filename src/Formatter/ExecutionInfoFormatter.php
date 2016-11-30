<?php
namespace Workana\AsyncJobs\Formatter;

use Workana\AsyncJobs\ExecutionInfo;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class ExecutionInfoFormatter extends AggregateFormatterAware
{
    const ONE_MILLION = 1000000;

    /**
     * {@inheritdoc}
     */
    public function format($target)
    {
        $rawInfo = $target->toArray();
        $info = [
            '[',
            '   Execution time: ' . $this->formatTime($rawInfo),
            '   Queue: ' . $rawInfo['queueName'],
            '   Preferred queue: ' . $rawInfo['preferredQueue'] ?: '(none)',
            '   Delay: ' . $rawInfo['delay'] . ' seconds',
            '   Retry: ' . $rawInfo['retries'] . ' / ' . $rawInfo['maxRetries'],
            ']',
        ];

        return implode(PHP_EOL, $info);
    }

    public function formatTime(array &$info)
    {
        return strtr(':number :unit', [':number' => round($info['executionTime'], 3), ':unit' => 'seconds']);
    }

    /**
     * {@inheritdoc}
     */
    public function canFormat($target)
    {
        return ($target instanceof ExecutionInfo);
    }
}