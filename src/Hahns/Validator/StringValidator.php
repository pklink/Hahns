<?php


namespace Hahns\Validator;


class StringValidator
{

    /**
     * @param string $v
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public static function hasTo($v, $name)
    {
        if (!is_string($v)) {
            $message = sprintf('`%s` has to be a string', $name);
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * @param string|null $v
     * @param string      $name
     * @throws \InvalidArgumentException
     */
    public static function hasToBeStringOrNull($v, $name)
    {
        if (!is_string($v) && !is_null($v)) {
            $message = sprintf('`%s` has to be a string or null', $name);
            throw new \InvalidArgumentException($message);
        }
    }
}
