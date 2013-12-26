<?php


namespace Hahns;


use Hahns\Exception\ParameterMustBeAnArrayException;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Exception\ServiceDoesNotExistException;
use Hahns\Exception\ServiceMustBeAnObjectException;

class Services
{

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @param string   $name
     * @param \Closure $callable
     * @param array    $args
     * @throws Exception\ParameterMustBeAStringException
     * @throws Exception\ParameterMustBeAnArrayException
     */
    public function register($name, \Closure $callable, $args = [])
    {
        if (!is_string($name)) {
            $message = 'Parameter `name` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        if (!is_array($args)) {
            $message = 'Parameter `args` must be an array';
            throw new ParameterMustBeAnArrayException($message);
        }

        $this->services[$name] = [
            'callable' => $callable,
            'args'     => $args
        ];
    }

    /**
     * @param string $name
     * @return object
     * @throws Exception\ServiceDoesNotExistException
     * @throws Exception\ServiceMustBeAnObjectException
     * @throws Exception\ParameterMustBeAStringException
     */
    public function get($name)
    {
        if (!is_string($name)) {
            $message = 'Parameter `name` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        if (!isset($this->services[$name])) {
            $message = sprintf('Service `%s` does not exist', $name);
            throw new ServiceDoesNotExistException($message);
        }

        // get service
        $service = &$this->services[$name];

        // initialize service
        if (!isset($service['instance'])) {
            $service['instance'] = call_user_func_array($service['callable'], $service['args']);

            if (!is_object($service['instance'])) {
                $message = sprintf('Service `%s` must be an object', $name);
                throw new ServiceMustBeAnObjectException($message);
            }
        }

        return $service['instance'];
    }
}
