<?php


namespace Hahns;


use Hahns\Exception\ParameterCallbackReturnsWrongTypeException;
use Hahns\Exception\ParameterIsNotRegisterException;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Exception\ParameterMustBeAStringOrNullException;
use Hahns\Exception\RouteNotFoundException;
use Hahns\Response\JsonImpl;

class Hahns
{

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ServiceHolder
     */
    protected $serviceHolder;

    /**
     * @var \Closure[]
     */
    protected $onNotFound = [];

    public function __construct()
    {
        $this->router           = new Router();
        $this->serviceHolder = new ServiceHolder();

        // register built-in parameters
        $this->parameter('Hahns\\Request', function () {
            // create request object
            $request = new Request();

            // fill request instance
            foreach ($this->router->getNamedParameters() as $name => $value) {
                $request->set($name, $value);
            }

            return $request;
        });

        $this->parameter('Hahns\\Response\\JsonImpl', function () {
            return new JsonImpl();
        });

        $this->parameter('Hahns\\ServiceHolder', function () {
            return $this->serviceHolder;
        });
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function delete($route, \Closure $callback)
    {
        $route = 'delete-' . $this->removeLastSlash($route);
        $this->router->add($route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function get($route, \Closure $callback)
    {
        $route = 'get-' . $this->removeLastSlash($route);
        $this->router->add($route, $callback);
    }

    /**
     * @param \Closure $callback
     */
    public function notFound(\Closure $callback)
    {
        $this->onNotFound[] = $callback;
    }

    /**
     * @param string $type
     * @param \Closure $callback
     * @throws Exception\ParameterMustBeAStringException
     */
    public function parameter($type, \Closure $callback)
    {
        if (!is_string($type)) {
            $message = 'Parameter `type` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        // remove first backslash
        if (strlen($type) > 0 && $type{0} == '\\') {
            $type = substr($type, 1);
        }

        $this->parameters[$type] = $callback;
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function patch($route, \Closure $callback)
    {
        $route = 'patch-' . $this->removeLastSlash($route);
        $this->router->add($route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function post($route, \Closure $callback)
    {
        $route = 'post-' . $this->removeLastSlash($route);
        $this->router->add($route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function put($route, \Closure $callback)
    {
        $route = 'put-' . $this->removeLastSlash($route);
        $this->router->add($route, $callback);
    }

    /**
     * @param string $path
     * @return string
     */
    private function removeLastSlash($path)
    {
        $lastPos = strlen($path) - 1;

        // remove last '/'
        if ($lastPos >= 0 && $path{$lastPos} == '/') {
            $path = substr($path, 0, -1);
        }

        return $path;
    }

    /**
     * @throws Exception\ParameterMustBeAStringOrNullException
     */
    public function run($route = null)
    {
        if (!is_string($route) && !is_null($route)) {
            $message = 'Parameter `route` must be a string or null';
            throw new ParameterMustBeAStringOrNullException($message);
        }

        // get route
        if ($route !== null) {
            $route = $this->removeLastSlash($route);
        } elseif (isset($_SERVER['PATH_INFO'])) {
            $route = $this->removeLastSlash($_SERVER['PATH_INFO']);
        } else {
            $route = '';
        }

        // get method and concat with $route
        $route = strtolower($_SERVER['REQUEST_METHOD']) . '-' . $route;

        try {
            $this->router->dispatch($route);

            // get callback
            $callback = $this->router->getCallback();

            // get attributes for callback
            $attributes         = [];
            $callbackReflection = new \ReflectionFunction($callback);

            foreach ($callbackReflection->getParameters() as $parameter) {
                $type = $parameter->getClass()->getName();

                // check if type exist
                if (!isset($this->parameters[$type])) {
                    $message = sprintf('Type `%s is not register. See Hahns::parameter()`', $type);
                    throw new ParameterIsNotRegisterException($message);
                }

                // create instance of parameter
                $parameterInstance = call_user_func($this->parameters[$type]);

                // check if parameter callback returned a valid instance
                if (!($parameterInstance instanceof $type)) {
                    $message = sprintf(
                        'Callback for parameter of type `%s` must be return an instance of `%s`',
                        $type,
                        $type
                    );
                    throw new ParameterCallbackReturnsWrongTypeException($message);
                }

                $attributes[] = $parameterInstance;
            }

            // call callback
            echo call_user_func_array($callback, $attributes);

        } catch (RouteNotFoundException $e) {
            foreach ($this->onNotFound as $callback) {
                call_user_func($callback);
            }

            header('HTTP/1.0 404 Not Found');
        }
    }

    /**
     * @param string $name
     * @param \Closure $callback
     */
    public function service($name, \Closure $callback)
    {
        $this->serviceHolder->register($name, $callback);
    }
}
