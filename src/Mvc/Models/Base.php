<?php
/**
 * Holds Base.
 *
 * @package WALib
 */
namespace WALib\Mvc\Models;

/**
 * Base class for models.
 *
 * A base model for storing data.
 *
 * @package WALib
 */
class Base
{
    /**
     * The storage for the models data.
     *
     * @var mixed[]
     */
    protected $_data = array();

    /**
     * Returns data if it exists in the models data array.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if(isset($this->_data[$name]))
        {
            return $this->_data[$name];
        }
        else
        {
            return null;
        }
    }

    /**
     * Saves data into the model.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }
}