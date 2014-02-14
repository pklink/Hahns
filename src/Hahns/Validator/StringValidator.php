<?php


namespace Hahns\Validator;


use Hahns\Exception\VariableHasToBeAStringException;
use Hahns\Exception\VariableHasToBeAStringOrNullException;

class StringValidator
{

    /**
     * @param string $v
     * @param string $name
     * @throws \Hahns\Exception\VariableHasToBeAStringException
     */
    public static function hasTo($v, $name)
    {
        if (!is_string($v)) {
            $message = sprintf('`%s` has to be a string', $name);
            throw new VariableHasToBeAStringException($message);
        }
    }

    /**
     * @param string|null $v
     * @param string      $name
     * @throws \Hahns\Exception\VariableHasToBeAStringOrNullException
     */
    public static function hasToBeStringOrNull($v, $name)
    {
        if (!is_string($v) && !is_null($v)) {
            $message = sprintf('`%s` has to be a string or null', $name);
            throw new VariableHasToBeAStringOrNullException($message);
        }
    }
}
