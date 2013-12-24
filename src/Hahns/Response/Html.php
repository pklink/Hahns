<?php


namespace Hahns\Response;

class Html extends AbstractImpl
{

    /**
     * @param string $data
     * @param array $headers
     * @return string
     */
    public function send($data, $headers = [])
    {
        $this->header('Content-Type', 'text/html');
        return parent::send($data, $headers);
    }
}
