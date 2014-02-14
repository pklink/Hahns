<?php


namespace Hahns\Validator\Argument;


use Hahns\Exception\ArgumentMustBeAStringException;
use Hahns\Exception\ArgumentMustBeAStringOrNullException;

class StringValidator
{

    /**
     * @param string $v
     * @param string $name
     * @throws \Hahns\Exception\ArgumentMustBeAStringException
     */
    public static function string($v, $name)
    {
        if (!is_string($v)) {
            $message = sprintf('Argument for `%s` must be a string', $name);
            throw new ArgumentMustBeAStringException($message);
        }
    }

    /**
     * @param string|null $v
     * @param string      $name
     * @throws \Hahns\Exception\ArgumentMustBeAStringOrNullException
     */
    public static function stringOrNull($v, $name)
    {
        if (!is_string($v) && !is_null($v)) {
            $message = sprintf('Argument for `%s` must be a string or null', $name);
            throw new ArgumentMustBeAStringOrNullException($message);
        }
    }
}
