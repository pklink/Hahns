<?php


namespace Hahns\Response;

use Hahns\Exception\DataMustBeAnArrayOrAnObjectException;
use Hahns\Exception\HeadersParameterMustBeAnArrayException;

class JsonImpl extends AbstractImpl
{

    /**
     * @param array|object $data
     * @param array $headers
     * @return string
     * @throws \Hahns\Exception\DataMustBeAnArrayOrAnObjectException
     * @throws \Hahns\Exception\HeadersParameterMustBeAnArrayException
     */
    public function send($data, $headers = [])
    {
        if (!is_array($data) && !is_object($data)) {
            throw new DataMustBeAnArrayOrAnObjectException();
        }

        if (!is_array($headers)) {
            throw new HeadersParameterMustBeAnArrayException();
        }

        $this->header('Content-Type', 'application/json');

        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }

        return json_encode($data);
    }
}
