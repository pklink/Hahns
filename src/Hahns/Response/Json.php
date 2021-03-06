<?php


namespace Hahns\Response;

use Hahns\Response;

class Json extends AbstractImpl
{

    /**
     * @param array|object $data
     * @param int|null     $status
     * @return string
     * @throws \InvalidArgumentException
     */
    public function send($data, $status = null)
    {
        if (!is_array($data) && !is_object($data)) {
            $message = 'Argument for `data` must be an array or an object';
            throw new \InvalidArgumentException($message);
        }

        $this->header('Content-Type', 'application/json');
        return parent::send(json_encode($data), $status);
    }
}
