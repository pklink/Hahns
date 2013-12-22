<?php


namespace Hahns\Response;

class JsonImpl extends AbstractImpl
{

    /**
     * @param mixed $data
     * @param array $header
     * @return string
     */
    public function send($data, $header = [])
    {
        $this->header('Content-Type', 'application/json');

        foreach ($header as $name => $value) {
            $this->header($name, $value);
        }

        return json_encode($data);
    }
}
