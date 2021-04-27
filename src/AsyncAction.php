<?php
namespace Workana\AsyncJobs;

/**
 * Async action
 */
class AsyncAction extends Job
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var Parameter[]
     */
    protected $parameters;

    public function __construct($class, $method, array $parameters = [])
    {
        $this->class = (string) $class;
        $this->method = (string) $method;
        $this->parameters = array_map(function($paramValue) {
            return new Parameter($paramValue);
        }, $parameters);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getParameterValues()
    {
        return array_map(function(Parameter $param) {
            return $param->getValue();
        }, $this->parameters);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->class}::{$this->method}";
    }
}