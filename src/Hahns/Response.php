<?php


namespace Hahns;


interface Response
{

    /**
     * @param mixed $data
     * @param array $header
     * @return string
     */
    public function send($data, $header = []);
}
