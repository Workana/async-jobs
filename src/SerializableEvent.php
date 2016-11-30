<?php
namespace Workana\AsyncJobs;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class SerializableEvent extends Event
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->parameters[(string) $name] = new Parameter($value, $name);
    }

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name]->getValue();
        } else {
            return $default;
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * Get all parameters
     *
     * @return array
     */
    public function getAll()
    {
        $result = [];

        foreach ($this->parameters as $name => $parameter) {
            $result[$name] = $parameter->getValue();
        }

        return $result;
    }
}