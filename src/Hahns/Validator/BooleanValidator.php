<?php


namespace Hahns\Validator;


class BooleanValidator
{

    /**
     * @param boolean $v
     * @param string  $name
     * @throws \InvalidArgumentException
     */
    public static function hasTo($v, $name)
    {
        if (!is_bool($v)) {
            $message = sprintf('`%s` has to be a boolean', $name);
            throw new \InvalidArgumentException($message);
        }
    }
}
