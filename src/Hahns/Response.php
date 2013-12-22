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
     * @param array $header
     * @return string
     */
    public function send($data, $header = []);
}
