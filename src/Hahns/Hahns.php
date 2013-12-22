<?php


namespace Hahns;


class Hahns
{

    /**
     * @var Router
     */
    protected $router;

    public function __construct($parsable = null)
    {
        $this->router = new Router($parsable);
    }

    /**
     * @param string $route
     * @param $callback
     */
    public function delete($route, $callback)
    {
        $this->router->add('DELETE', $route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     */
    public function get($route, $callback)
    {
        $this->router->add('GET', $route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     */
    public function patch($route, $callback)
    {
        $this->router->add('PATCH', $route, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     */
    public function post($route, $callback)
    {
        $this->router->add('POST', $route, $callback);
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->router->dispatch();
    }
}
