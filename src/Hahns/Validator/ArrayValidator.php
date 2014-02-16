<?php


namespace Hahns\Validator;


class ArrayValidator
{

    /**
     * @param mixed  $v
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public static function hasTo($v, $name)
    {
        if (!is_array($v)) {
            $message = sprintf('`%s` has to be an array', $name);
            throw new \InvalidArgumentException($message);
        }
    }
}
