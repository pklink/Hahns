<?php


namespace Hahns\Response;


use Hahns\Response;

class JsonImpl implements Response
{

    /**
     * @param mixed $data
     * @param array $header
     * @return string
     */
    public function send($data, $header = [])
    {
        header('Content-Type: application/json');
        return json_encode($data);
    }
}
