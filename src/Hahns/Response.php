<?php


namespace Hahns;


class Response
{

    /**
     * @param mixed $data
     * @param string $contentType
     * @return string
     */
    public function json($data, $contentType = 'application/json')
    {
        header('Content-Type: ' . $contentType);
        return json_encode($data);
    }
}

