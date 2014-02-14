<?php


namespace Hahns\Validator;


use Hahns\Exception\ArgumentMustBeAnIntegerException;

class IntegerValidator
{

    /**
     * @param int    $v
     * @param string $name
     * @throws \Hahns\Exception\ArgumentMustBeAnIntegerException
     */
    public static function hasTo($v, $name)
    {
        if (!is_int($v)) {
            $message = sprintf('Argument for `%s` must be an integer', $name);
            throw new ArgumentMustBeAnIntegerException($message);
        }
    }
}
