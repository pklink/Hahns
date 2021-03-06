<?php


namespace Hahns;


use Hahns\Exception\ServiceDoesNotExistException;
use Hahns\Exception\ServiceHasToBeAnObjectException;
use Hahns\Validator\ArrayValidator;
use Hahns\Validator\StringValidator;

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
     * @throws \InvalidArgumentException
     */
    public function register($name, \Closure $callable, $args = [])
    {
        StringValidator::hasTo($name, 'name');
        ArrayValidator::hasTo($args, 'args');

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
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        StringValidator::hasTo($name, 'name');

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
