<?php


namespace Hahns\Response;

use Hahns\Exception\ParameterMustBeAnArrayOrAnObjectException;

class Json extends AbstractImpl
{

    /**
     * @param array|object $data
     * @param array $headers
     * @return string
     * @throws \Hahns\Exception\ParameterMustBeAnArrayOrAnObjectException
     */
    public function send($data, $headers = [])
    {
        if (!is_array($data) && !is_object($data)) {
            $message = 'Parameter `data` must be an array or an object';
            throw new ParameterMustBeAnArrayOrAnObjectException($message);
        }

        $this->header('Content-Type', 'application/json');
        return parent::send(json_encode($data), $headers);
    }
}
