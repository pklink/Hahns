<?php


namespace Hahns\Response;


use Hahns\Response;

abstract class AbstractImpl implements Response
{

    /**
     * @param string $name
     * @param string $value
     */
    public function header($name, $value)
    {
        header(sprintf('%s: %s', $name, $value));
    }
}
