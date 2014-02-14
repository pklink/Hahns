<?php


namespace Hahns;


use Hahns\Exception\VariableHasToBeAStringException;

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
     * @throws Exception\VariableHasToBeAStringException
     */
    public function get($name, $default = null)
    {
        if (!is_string($name)) {
            $message = 'Argument for `name` must be a string';
            throw new VariableHasToBeAStringException($message);
        }

        if (!isset($this->config[$name])) {
            return $default;
        }

        return $this->config[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws Exception\VariableHasToBeAStringException
     */
    public function set($name, $value)
    {
        if (!is_string($name)) {
            $message = 'Argument for `name` must be a string';
            throw new VariableHasToBeAStringException($message);
        }

        $this->config[$name] = $value;
    }
}
