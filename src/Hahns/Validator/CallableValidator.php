<?php


namespace Hahns\Validator;


use Hahns\Exception\VariableHasToBeCallableException;

class CallableValidator
{

    /**
     * @param mixed  $v
     * @param string $name
     * @throws \Exception
     */
    public static function hasTo($v, $name)
    {
        if (!is_callable($v)) {
            $message = sprintf('`%s` has to be callable', $name);
            throw new VariableHasToBeCallableException($message);
        }
    }
}