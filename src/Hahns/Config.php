<?php


namespace Hahns;


use Hahns\Validator\StringValidator;

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
        StringValidator::hasTo($name, 'name');

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
        StringValidator::hasTo($name, 'name');
        $this->config[$name] = $value;
    }
}
