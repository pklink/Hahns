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
     * @param \Closure $callback
     */
    public function delete($route, \Closure $callback)
    {
        $this->router->add('DELETE', $route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function get($route, \Closure $callback)
    {
        $this->router->add('GET', $route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function patch($route, \Closure $callback)
    {
        $this->router->add('PATCH', $route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function post($route, \Closure $callback)
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
