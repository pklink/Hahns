<?php


namespace Hahns\Response;

use Hahns\Exception\ParameterMustBeAnArrayException;
use Hahns\Exception\ParameterMustBeAnArrayOrAnObjectException;

class Json extends AbstractImpl
{

    /**
     * @param array|object $data
     * @param array $headers
     * @return string
     * @throws \Hahns\Exception\ParameterMustBeAnArrayOrAnObjectException
     * @throws \Hahns\Exception\ParameterMustBeAnArrayException
     */
    public function send($data, $headers = [])
    {
        if (!is_array($data) && !is_object($data)) {
            $message = 'Parameter `data` must be an array or an object';
            throw new ParameterMustBeAnArrayOrAnObjectException($message);
        }

        if (!is_array($headers)) {
            $message = 'Parameter `headers` must be an array';
            throw new ParameterMustBeAnArrayException($message);
        }

        $this->header('Content-Type', 'application/json');

        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }

        return json_encode($data);
    }
}
