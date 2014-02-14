<?php


namespace Hahns\Validator\Argument;


use Hahns\Exception\ArgumentMustBeABooleanException;

class BooleanValidator
{

    /**
     * @param boolean $v
     * @param string  $name
     * @throws \Hahns\Exception\ArgumentMustBeABooleanException
     */
    public static function boolean($v, $name)
    {
        if (!is_bool($v)) {
            $message = sprintf('Argument for `%s` must be a boolean', $name);
            throw new ArgumentMustBeABooleanException($message);
        }
    }
}
