<?php
namespace Workana\AsyncJobs\Formatter;

use Workana\AsyncJobs\Util\ClassUtils;
use Workana\AsyncJobs\Doctrine\QueueableEntityInterface;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class QueueableEntityFormatter extends AggregateFormatterAware
{

    /**
     * {@inheritdoc}
     */
    public function format($target)
    {
        $actualClass = current(array_reverse(explode('\\', ClassUtils::getRealClass($target))));

        return strtr('Entity#:entityClass with key = :id', [
            ':entityClass' => $actualClass,
            ':id' => $this->formatId($target),
        ]);
    }

    /**
     * @param QueueableEntityInterface $target
     * @return string
     */
    protected function formatId(QueueableEntityInterface $target)
    {
        $id = $target->getId();

        if (is_array($id)) {
            $joinedArray = implode(', ', $id);

            return "[{$joinedArray}]";
        }  {
            return (string) $id;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canFormat($target)
    {
        if (!is_object($target)) {
            return false;
        }

        return is_object($target) && is_a(ClassUtils::getRealClass($target), QueueableEntityInterface::class, true);
    }
}