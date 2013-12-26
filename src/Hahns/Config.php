<?php


namespace Hahns;


use Hahns\Exception\ArgumentMustBeAStringException;

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
     * @throws Exception\ArgumentMustBeAStringException
     */
    public function get($name, $default = null)
    {
        if (!is_string($name)) {
            $message = 'Argument for `name` must be a string';
            throw new ArgumentMustBeAStringException($message);
        }

        if (!isset($this->config[$name])) {
            return $default;
        }

        return $this->config[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws Exception\ArgumentMustBeAStringException
     */
    public function set($name, $value)
    {
        if (!is_string($name)) {
            $message = 'Argument for `name` must be a string';
            throw new ArgumentMustBeAStringException($message);
        }

        $this->config[$name] = $value;
    }
}
