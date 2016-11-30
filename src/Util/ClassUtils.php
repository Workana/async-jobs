<?php
namespace Workana\AsyncJobs\Util;


use Doctrine\ORM\Proxy\Proxy;

class ClassUtils
{
    /**
     * Get real class
     *
     * @param object|string $target
     *
     * @return string
     */
    public static function getRealClass($target)
    {
        if (!is_object($target)) {
            return '';
        }

        if (is_a($target, Proxy::class, true)) {
            return get_parent_class($target);
        } else {
            return is_string($target) ? $target : get_class($target);
        }
    }
}