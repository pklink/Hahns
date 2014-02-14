<?php


namespace Hahns\Response;


use Hahns\Exception\ArgumentMustBeAStringException;
use Hahns\Exception\StatusMessageCannotFindException;
use Hahns\Response;
use Hahns\Validator\Argument\IntegerValidator;
use Hahns\Validator\Argument\StringValidator;

abstract class AbstractImpl implements Response
{

    /**
     * @param string $name
     * @param string $value
     * @throws \Hahns\Exception\ArgumentMustBeAStringException
     */
    public function header($name, $value)
    {
        if (!is_string($name)) {
            $message = 'Argument for `name` must be a string';
            throw new ArgumentMustBeAStringException($message);
        }

        if (!is_string($value)) {
            $message = 'Argument for `value` must be a string';
            throw new ArgumentMustBeAStringException($message);
        }

        header(sprintf('%s: %s', $name, $value));
    }

    /**
     * @param string $location
     * @param int $status
     * @throws \Hahns\Exception\ArgumentMustBeAStringException
     */
    public function redirect($location, $status = Response::CODE_MOVED_PERMANENTLY)
    {
        if (!is_string($location)) {
            $message = 'Argument for `location` must be a string';
            throw new ArgumentMustBeAStringException($message);
        }

        $this->status($status);
        $this->header('Location', $location);
    }

    /**
     * @param string   $data
     * @param int|null $status
     * @return string
     * @throws \Hahns\Exception\ArgumentMustBeAStringException
     */
    public function send($data, $status = null)
    {
        if (!is_string($data)) {
            $message = 'Argument for `data` must be a string';
            throw new ArgumentMustBeAStringException($message);
        }

        if (!is_null($status)) {
            $this->status($status);
        }

        return $data;
    }

    /**
     * @param int $code
     * @param string|null $message
     * @param string $httpVersion
     * @throws \Hahns\Exception\StatusMessageCannotFindException
     * @throws \Hahns\Exception\ArgumentMustBeAnIntegerException
     * @throws \Hahns\Exception\ArgumentMustBeAStringOrNullException
     * @throws \Hahns\Exception\ArgumentMustBeAStringException
     */
    public function status($code = Response::CODE_OK, $message = null, $httpVersion = '1.1')
    {
        IntegerValidator::integer($code, 'code');
        StringValidator::stringOrNull($message, 'message');

        if (!is_string($httpVersion)) {
            $message = 'Argument for `httpVersion` must be a string';
            throw new ArgumentMustBeAStringException($message);
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
