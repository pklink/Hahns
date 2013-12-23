<?php


namespace Hahns;


interface Response
{

    /**
     * @param string $name
     * @param string $value
     */
    public function header($name, $value);

    /**
     * @param mixed $data
     * @param array $headers
     * @return string
     */
    public function send($data, $headers = []);
}
