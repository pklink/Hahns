<?php


namespace Hahns\Response;


use Hahns\Exception\StatusMessageCannotFindException;
use Hahns\Response;
use Hahns\Validator\IntegerValidator;
use Hahns\Validator\StringValidator;

abstract class AbstractImpl implements Response
{

    /**
     * @param string $name
     * @param string $value
     * @throws \Hahns\Exception\VariableHasToBeAStringException
     */
    public function header($name, $value)
    {
        StringValidator::hasTo($name, 'name');
        StringValidator::hasTo($value, 'value');
        header(sprintf('%s: %s', $name, $value));
    }

    /**
     * @param string $location
     * @param int $status
     * @throws \Hahns\Exception\VariableHasToBeAStringException
     */
    public function redirect($location, $status = Response::CODE_MOVED_PERMANENTLY)
    {
        StringValidator::hasTo($location, 'location');
        $this->status($status);
        $this->header('Location', $location);
    }

    /**
     * @param string   $data
     * @param int|null $status
     * @return string
     * @throws \Hahns\Exception\VariableHasToBeAStringException
     */
    public function send($data, $status = null)
    {
        StringValidator::hasTo($data, 'data');

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
     * @throws \Hahns\Exception\VariableHasToBeAnIntegerException
     * @throws \Hahns\Exception\VariableHasToBeAStringOrNullException
     * @throws \Hahns\Exception\VariableHasToBeAStringException
     */
    public function status($code = Response::CODE_OK, $message = null, $httpVersion = '1.1')
    {
        IntegerValidator::hasTo($code, 'code');
        StringValidator::hasToBeStringOrNull($message, 'message');
        StringValidator::hasTo($httpVersion, 'httpVersion');

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
