<?php


namespace Hahns;


use Hahns\Exception\VariableHasToBeAnArrayException;
use Hahns\Exception\VariableHasToBeAStringException;
use Hahns\Exception\ServiceDoesNotExistException;
use Hahns\Exception\ServiceHasToBeAnObjectException;

class ServiceHolder
{

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @param string   $name
     * @param \Closure $callable
     * @param array    $args
     * @throws Exception\VariableHasToBeAStringException
     * @throws Exception\VariableHasToBeAnArrayException
     */
    public function register($name, \Closure $callable, $args = [])
    {
        if (!is_string($name)) {
            $message = 'Argument for `name` must be a string';
            throw new VariableHasToBeAStringException($message);
        }

        if (!is_array($args)) {
            $message = 'Argument for `args` must be an array';
            throw new VariableHasToBeAnArrayException($message);
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
     * @throws Exception\ServiceHasToBeAnObjectException
     * @throws Exception\VariableHasToBeAStringException
     */
    public function get($name)
    {
        if (!is_string($name)) {
            $message = 'Argument for `name` must be a string';
            throw new VariableHasToBeAStringException($message);
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
                throw new ServiceHasToBeAnObjectException($message);
            }
        }

        return $service['instance'];
    }
}
