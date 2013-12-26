<?php


namespace Hahns\Response;

use Hahns\Response;

class Html extends AbstractImpl
{

    /**
     * @param string   $data
     * @param int|null $status
     * @return string
     */
    public function send($data, $status = null)
    {
        $this->header('Content-Type', 'text/html');
        return parent::send($data, $status);
    }
}
