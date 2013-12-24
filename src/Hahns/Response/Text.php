<?php


namespace Hahns\Response;

class Text extends AbstractImpl
{

    /**
     * @param string $data
     * @param array $headers
     * @return string
     */
    public function send($data, $headers = [])
    {
        $this->header('Content-Type', 'text/plain');
        return parent::send($data, $headers);
    }
}
