<?php


namespace Hahns\Response;


use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Response;

abstract class AbstractImpl implements Response
{

    /**
     * @param string $name
     * @param string $value
     * @throws \Hahns\Exception\ParameterMustBeAStringException
     */
    public function header($name, $value)
    {
        if (!is_string($name)) {
            $message = 'Parameter `name` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        if (!is_string($value)) {
            $message = 'Parameter `value` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        header(sprintf('%s: %s', $name, $value));
    }
}
