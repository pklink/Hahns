<?php


namespace Hahns\Validator;


use Hahns\Exception\ArgumentMustBeAStringException;
use Hahns\Exception\ArgumentMustBeAStringOrNullException;

class StringValidator
{

    /**
     * @param string $v
     * @param string $name
     * @throws \Hahns\Exception\ArgumentMustBeAStringException
     */
    public static function hasTo($v, $name)
    {
        if (!is_string($v)) {
            $message = sprintf('`%s` has to be a string', $name);
            throw new ArgumentMustBeAStringException($message);
        }
    }

    /**
     * @param string|null $v
     * @param string      $name
     * @throws \Hahns\Exception\ArgumentMustBeAStringOrNullException
     */
    public static function hasToBeStringOrNull($v, $name)
    {
        if (!is_string($v) && !is_null($v)) {
            $message = sprintf('`%s` has to be a string or null', $name);
            throw new ArgumentMustBeAStringOrNullException($message);
        }
    }
}
