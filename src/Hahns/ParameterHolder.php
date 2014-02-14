<?php


namespace Hahns;


use Hahns\Exception\ParameterCallbackReturnsWrongTypeException;
use Hahns\Exception\ParameterDoesNotExistException;
use Hahns\Validator\BooleanValidator;
use Hahns\Validator\StringValidator;

class ParameterHolder
{

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @param string   $type
     * @param \Closure $callback
     * @param bool     $asSingleton
     * @param array    $args
     * @throws Exception\ArgumentMustBeAStringException
     * @throws Exception\ArgumentMustBeABooleanException
     */
    public function register($type, \Closure $callback, $asSingleton = true, $args = [])
    {
        StringValidator::string($type, 'type');
        BooleanValidator::boolean($asSingleton, 'asSingleton');

        // remove first backslash
        if (strlen($type) > 0 && $type{0} == '\\') {
            $type = substr($type, 1);
        }

        $this->parameters[$type] = [
            'isSingleton' => $asSingleton,
            'callback'    => $callback,
            'instance'    => null,
            'arg'         => $args
        ];
    }

    /**
     * @param string $type
     * @return object
     * @throws Exception\ParameterDoesNotExistException
     * @throws Exception\ParameterCallbackReturnsWrongTypeException
     */
    public function get($type)
    {
        // check if type exist
        if (!isset($this->parameters[$type])) {
            $message = sprintf('Type `%s does not exist. See Hahns::parameter()`', $type);
            throw new ParameterDoesNotExistException($message);
        }

        // get instance
        if (is_object($this->parameters[$type]['instance'])) {
            $instance = $this->parameters[$type]['instance'];
        } else {
            $instance = call_user_func($this->parameters[$type]['callback'], $this);
        }

        // check if parameter callback returned a valid instance
        if (!($instance instanceof $type)) {
            $message = sprintf(
                'Callback for parameter of type `%s` must be return an instance of `%s`',
                $type,
                $type
            );
            throw new ParameterCallbackReturnsWrongTypeException($message);
        }

        // save instance
        if ($this->parameters[$type]['isSingleton'] === true) {
            $this->parameters[$type]['instance'] = $instance;
        }

        return $instance;
    }
}
