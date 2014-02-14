<?php


namespace Hahns\Validator;


use Hahns\Exception\VariableHasToBeABooleanException;

class BooleanValidator
{

    /**
     * @param boolean $v
     * @param string  $name
     * @throws \Hahns\Exception\VariableHasToBeABooleanException
     */
    public static function hasTo($v, $name)
    {
        if (!is_bool($v)) {
            $message = sprintf('`%s` has to be a boolean', $name);
            throw new VariableHasToBeABooleanException($message);
        }
    }
}
