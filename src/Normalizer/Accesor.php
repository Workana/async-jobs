<?php
namespace Workana\AsyncJobs\Normalizer;

use ReflectionClass;
use ReflectionProperty;

/**
 * Property accesor
 *
 * @author Carlos Frutos <charly@workana.com>
 */
abstract class Accesor
{
    /**
     * Set protected/private property
     *
     * @param object $target
     * @param string $propertyName
     * @param mixed $value
     *
     * @return void
     */
    public static function set($target, $propertyName, $value)
    {
        $property = new ReflectionProperty($target, $propertyName);
        $property->setAccessible(true);
        $property->setValue($target, $value);
    }

    /**
     * Get protected/private property
     *
     * @param object $target
     * @param string $propertyName
     *
     * @return mixed
     */
    public static function get($target, $propertyName)
    {
        $property = new ReflectionProperty($target, $propertyName);
        $property->setAccessible(true);
        
        return $property->getValue($target);
    }

    /**
     * Creates a new instance without using constructor
     *
     * @param string $class
     *
     * @return mixed
     */
    public static function newInstanceWithoutConstructor($class)
    {
        $reflection = new ReflectionClass($class);

        return $reflection->newInstanceWithoutConstructor();
    }
}