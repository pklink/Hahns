<?php


namespace Hahns;


use Hahns\Validator\StringValidator;

class Request
{

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $payload = [];

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
     * @throws Exception\VariableHasToBeAStringException
     */
    public function header($name, $default = null)
    {
        StringValidator::hasTo($name, 'name');

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
        if (count($this->payload) === 0) {
            $fp = fopen('php://input', 'r');
            parse_str(stream_get_contents($fp), $this->payload);
            fclose($fp);
        }

        // check if payload still a array
        if (!is_array($this->payload)) {
            $this->payload = [];
        }

        return $this->getFrom($this->payload, $name, $default);
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
