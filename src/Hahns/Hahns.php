<?php


namespace Hahns;


use Hahns\Exception\RouteNotFoundException;
use Hahns\Response\JsonImpl;

class Hahns
{

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ServiceHolder
     */
    protected $serviceContainer;

    public function __construct($parsable = null)
    {
        $this->router           = new Router($parsable);
        $this->serviceContainer = new ServiceHolder();
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
     * @param string $name
     * @param \Closure $callback
     */
    public function service($name, \Closure $callback)
    {
        $this->serviceContainer->register($name, $callback);
    }

    /**
     * @param array $parameter
     * @return Request
     */
    private function createRequest($parameter)
    {
        // create request object
        $request = new Request();

        // fill request instance
        foreach ($parameter as $name => $value) {
            $request->set($name, $value);
        }

        return $request;
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            $this->router->dispatch();

            // get named parametes and callback
            $namedParameter = $this->router->getNamedParameters();
            $callback       = $this->router->getCallback();

            // get attributes for callback
            $attributes         = [];
            $callbackReflection = new \ReflectionFunction($callback);

            foreach ($callbackReflection->getParameters() as $parameter) {
                $type = $parameter->getClass()->name;

                switch ($type) {
                    case 'Hahns\\Request':
                        $attributes[] = $this->createRequest($namedParameter);
                        break;
                    case 'Hahns\\Response\\JsonImpl':
                        $attributes[] = new JsonImpl();
                        break;
                    case 'Hahns\\ServiceHolder':
                        $attributes[] = $this->serviceContainer;
                        break;
                }
            }

            // call callback
            switch (count($attributes)) {
                case 0:
                    echo call_user_func($callback);
                    break;
                case 1:
                    echo call_user_func($callback, $attributes[0]);
                    break;
                case 2:
                    echo call_user_func($callback, $attributes[0], $attributes[1]);
                    break;
                case 3:
                    echo call_user_func($callback, $attributes[0], $attributes[1], $attributes[2]);
                    break;
            }

        } catch (RouteNotFoundException $e) {
            echo "404";
        }
    }
}
