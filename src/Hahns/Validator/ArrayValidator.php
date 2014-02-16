<?php


namespace Hahns\Validator;


use Hahns\Exception\VariableHasToBeAnArrayException;

class ArrayValidator
{

    /**
     * @param mixed  $v
     * @param string $name
     * @throws VariableHasToBeAnArrayException
     */
    public static function hasTo($v, $name)
    {
        if (!is_array($v)) {
            $message = sprintf('`%s` has to be an array', $name);
            throw new VariableHasToBeAnArrayException($message);
        }
    }
}
