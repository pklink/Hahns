<?php


namespace Hahns\Validator;


use Hahns\Exception\VariableHasToBeAnIntegerException;

class IntegerValidator
{

    /**
     * @param int    $v
     * @param string $name
     * @throws \Hahns\Exception\VariableHasToBeAnIntegerException
     */
    public static function hasTo($v, $name)
    {
        if (!is_int($v)) {
            $message = sprintf('`%s` has to be an integer', $name);
            throw new VariableHasToBeAnIntegerException($message);
        }
    }
}
