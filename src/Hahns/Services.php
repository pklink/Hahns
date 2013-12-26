<?php


namespace Hahns;


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
     * @param string $name
     * @param \Closure $callable
     * @throws Exception\ParameterMustBeAStringException
     */
    public function register($name, \Closure $callable)
    {
        if (!is_string($name)) {
            $message = 'Parameter `name` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        $this->services[$name] = [
            'callable' => $callable
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
            $service['instance'] = call_user_func($service['callable']);

            if (!is_object($service['instance'])) {
                $message = sprintf('Service `%s` must be an object', $name);
                throw new ServiceMustBeAnObjectException($message);
            }
        }

        return $service['instance'];
    }
}
