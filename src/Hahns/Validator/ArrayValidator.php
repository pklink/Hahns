<?php


namespace Hahns\Validator;


use Hahns\Exception\VariableMustBeAnArrayException;

class ArrayValidator
{

    /**
     * @param mixed  $v
     * @param string $name
     * @throws \Exception
     */
    public static function hasTo($v, $name)
    {
        if (!is_array($v)) {
            $message = sprintf('`%s` has to be an array', $name);
            throw new VariableMustBeAnArrayException($message);
        }
    }
}
