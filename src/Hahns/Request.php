<?php


namespace Hahns;


class Request
{

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (!isset($this->params[$name])) {
            return $default;
        } else {
            return $this->params[$name];
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;
    }
}
