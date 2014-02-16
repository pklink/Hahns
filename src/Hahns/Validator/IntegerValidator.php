<?php


namespace Hahns\Validator;


class IntegerValidator
{

    /**
     * @param int    $v
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public static function hasTo($v, $name)
    {
        if (!is_int($v)) {
            $message = sprintf('`%s` has to be an integer', $name);
            throw new \InvalidArgumentException($message);
        }
    }
}
