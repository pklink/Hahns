<?php


namespace Hahns\Response;

use Hahns\Response;

class Text extends AbstractImpl
{

    /**
     * @param string   $data
     * @param int|null $status
     * @return string
     */
    public function send($data, $status = null)
    {
        $this->header('Content-Type', 'text/plain');
        return parent::send($data, $status);
    }
}
