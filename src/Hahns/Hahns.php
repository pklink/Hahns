<?php


namespace Hahns;


use Hahns\Exception\ParameterCallbackReturnsWrongTypeException;
use Hahns\Exception\ParameterIsNotRegisterException;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Exception\ParameterMustBeAStringOrNullException;
use Hahns\Exception\RouteNotFoundException;
use Hahns\Response\Html;
use Hahns\Response\Json;
use Hahns\Response\Text;

class Hahns
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Services
     */
    protected $services;

    /**
     * @var \Closure[]
     */
    protected $onNotFound = [];

    public function __construct()
    {
        $this->services = new Services();
        $this->config   = new Config();

        $this->registerBuiltInParameters();
    }

    /**
     * @param string $prefix
     * @param string $route
     * @param \Closure $callback
     * @throws Exception\ParameterMustBeAStringException
     */
    protected function addPrefixedRoute($prefix, $route, \Closure $callback)
    {
        if (!is_string($route)) {
            $message = 'Parameter `route` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        $route = sprintf('%s-%s', $prefix, $this->removeLastSlash($route));
        $this->router()->add($route, $callback);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @return mixed
     */
    public function config($name = null, $value = null)
    {
        if (!is_null($name) && !is_null($value)) {
            $this->config->set($name, $value);
            return null;
        } elseif (!is_null($name)) {
            return $this->config->get($name);
        } else {
            return $this->config;
        }
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function delete($route, \Closure $callback)
    {
        $this->addPrefixedRoute('delete', $route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function get($route, \Closure $callback)
    {
        $this->addPrefixedRoute('get', $route, $callback);
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function notFound(\Closure $callback)
    {
        $this->onNotFound[] = $callback;
        return $this;
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
        $this->addPrefixedRoute('patch', $route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function post($route, \Closure $callback)
    {
        $this->addPrefixedRoute('post', $route, $callback);
    }

    /**
     * @param string $route
     * @param \Closure $callback
     */
    public function put($route, \Closure $callback)
    {
        $this->addPrefixedRoute('put', $route, $callback);
    }

    /**
     * return void
     */
    protected function registerBuiltInParameters()
    {
        $this->parameter('Hahns\\Request', function () {
            // create request object
            $request = new Request();

            // fill request instance
            foreach ($this->router()->getNamedParameters() as $name => $value) {
                $request->set($name, $value);
            }

            return $request;
        });

        $this->parameter('Hahns\\Hahns', function () {
            return $this;
        });

        $this->parameter('Hahns\\Config', function () {
            return $this->config;
        });

        $this->parameter('Hahns\\Response\\Json', function () {
            return new Json();
        });

        $this->parameter('Hahns\\Response\\Text', function () {
            return new Text();
        });

        $this->parameter('Hahns\\Response\\Html', function () {
            return new Html();
        });

        $this->parameter('Hahns\\Services', function () {
            return $this->services;
        });
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
     * @return \Hahns\Router
     */
    public function router()
    {
        if (is_null($this->router)) {
            $this->router = new Router();
        }

        return $this->router;
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
            $this->router()->dispatch($route);

            // get callback
            $callback = $this->router()->getCallback();

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
        $this->services->register($name, $callback);
    }

    /**
     * @return \Hahns\Services
     */
    public function services()
    {
        return $this->services;
    }
}
