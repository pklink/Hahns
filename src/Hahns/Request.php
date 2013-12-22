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
        if (isset($this->params[$name])) {
            return $this->params[$name];
        } elseif (isset($_GET[$name])) {
            return $_GET[$name];
        } else {
            return $default;
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

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function payload($name, $default = null)
    {
        $fp = fopen('php://input', 'r');
        parse_str(stream_get_contents($fp), $params);
        fclose($fp);

        if (isset($params[$name])) {
            return $params[$name];
        } else {
            return $default;
        }
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function post($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        } else {
            return $default;
        }
    }
}
