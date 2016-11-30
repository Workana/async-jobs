<?php
namespace Workana\AsyncJobs\Formatter;

use Workana\AsyncJobs\AsyncAction;

class AsyncActionFormatter extends AggregateFormatterAware
{
    /**
     * {@inheritdoc}
     */
    public function canFormat($target)
    {
        return ($target instanceof AsyncAction);
    }

    /**
     * {@inheritdoc}
     */
    public function format($target)
    {
        $formattedParameters = array_map(function($value) {
            return '    ' . $this->aggregate->format($value);
        }, $target->getParameterValues());

        array_unshift($formattedParameters, '[');
        array_push($formattedParameters, ']');

        $paramDescription = implode(PHP_EOL, $formattedParameters);

        return strtr(':jobName :jobGroup with parameters :parametersDescription', [
            ':jobName' => $target->getName(),
            ':jobGroup' => (string) $target,
            ':parametersDescription' => $paramDescription,
        ]);
    }
}