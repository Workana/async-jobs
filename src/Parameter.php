<?php
namespace Workana\AsyncJobs;
use Workana\AsyncJobs\Util\ClassUtils;

/**
 * DTO Parameter
 *
 * @author Carlos Frutos <charly@workana.com>
 */
class Parameter
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $scalar = false;

    /**
     * @var string?
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Creates a new Parameter
     *
     * @param mixed $value
     * @param string? $name
     */
    public function __construct($value, $name = null)
    {
        if (is_scalar($value) || is_array($value) || is_null($value)) {
            $this->type = gettype($value);
            $this->scalar = true;
        } else {
            $this->type = ClassUtils::getRealClass($value);
        }

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isScalar()
    {
        return $this->scalar;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}