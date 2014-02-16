<?php


namespace Hahns\Validator;


class CallableValidator
{

    /**
     * @param mixed  $v
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public static function hasTo($v, $name)
    {
        if (!is_callable($v)) {
            $message = sprintf('`%s` has to be callable', $name);
            throw new \InvalidArgumentException($message);
        }
    }
}