<?php


namespace Hahns;


use Hahns\Exception\ParameterMustBeAStringException;

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
     * @param array $data
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    private function getFrom($data, $name, $default = null)
    {
        if (!is_array($data)) {
            return $default;
        }

        if (isset($data[$name])) {
            return $data[$name];
        } else {
            return $default;
        }
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @throws Exception\ParameterMustBeAStringException
     */
    public function header($name, $default = null)
    {
        if (!is_string($name)) {
            $message = 'Parameter `name` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        $name = sprintf('HTTP_%s', strtoupper($name));
        $name = str_replace('-', '_', $name);

        if (isset($_SERVER[$name])) {
            return $_SERVER[$name];
        } else {
            return $default;
        }
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

        return $this->getFrom($params, $name, $default);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function post($name, $default = null)
    {
        return $this->getFrom($_POST, $name, $default);
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
