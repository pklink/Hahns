<?php


namespace Hahns\Response;


use Hahns\Exception\ParameterMustBeAnIntegerException;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Exception\ParameterMustBeAStringOrNullException;
use Hahns\Exception\StatusMessageCannotFindException;
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

    /**
     * @param string $location
     * @param int $code
     * @throws \Hahns\Exception\ParameterMustBeAStringException
     */
    public function redirect($location, $code = Response::CODE_MOVED_PERMANENTLY)
    {
        if (!is_string($location)) {
            $message = 'Parameter `location` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        $this->status($code);
        $this->header('Location', $location);
    }

    /**
     * @param int $code
     * @param string|null $message
     * @param string $httpVersion
     * @throws \Hahns\Exception\StatusMessageCannotFindException
     * @throws \Hahns\Exception\ParameterMustBeAnIntegerException
     * @throws \Hahns\Exception\ParameterMustBeAStringOrNullException
     * @throws \Hahns\Exception\ParameterMustBeAStringException
     */
    public function status($code = Response::CODE_OK, $message = null, $httpVersion = '1.1')
    {
        if (!is_int($code)) {
            $message = 'Parameter `status` must be an integer';
            throw new ParameterMustBeAnIntegerException($message);
        }

        if (!is_null($message) && !is_string($message)) {
            $message = 'Parameter `message` must be a string or null';
            throw new ParameterMustBeAStringOrNullException($message);
        }

        if (!is_string($httpVersion)) {
            $message = 'Paramter `httpVersion` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        // get message
        if (is_null($message)) {
            $constantName = sprintf('\\Hahns\\Response::MSG_%d', $code);

            if (!defined($constantName)) {
                $message = sprintf('Status message for code `%d` cannot find', $code);
                throw new StatusMessageCannotFindException($message);
            }

            $message = constant($constantName);
        }

        header(sprintf('HTTP/%s %d %s', $httpVersion, $code, $message));
    }
}
