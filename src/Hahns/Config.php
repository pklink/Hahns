<?php


namespace Hahns;


use Hahns\Exception\ParameterMustBeAStringException;

class Config
{

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @throws Exception\ParameterMustBeAStringException
     */
    public function get($name, $default = null)
    {
        if (!is_string($name)) {
            $message = 'Parameter `name` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        if (!isset($this->config[$name])) {
            return $default;
        }

        return $this->config[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws Exception\ParameterMustBeAStringException
     */
    public function set($name, $value)
    {
        if (!is_string($name)) {
            $message = 'Parameter `name` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        $this->config[$name] = $value;
    }
}
