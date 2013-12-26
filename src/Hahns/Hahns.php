<?php


namespace Hahns;


use Hahns\Exception\NotFoundException;
use Hahns\Exception\ParameterCallbackReturnsWrongTypeException;
use Hahns\Exception\ParameterIsNotRegisterException;
use Hahns\Exception\ParameterMustBeABooleanException;
use Hahns\Exception\ParameterMustBeAnIntegerException;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Exception\ParameterMustBeAStringOrNullException;
use Hahns\Response\Html;
use Hahns\Response\Json;
use Hahns\Response\Text;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Hahns
{

    const EVENT_NOT_FOUND              = 404;
    const EVENT_ERROR                  = 500;
    const EVENT_BEFORE_RUNNING         = 100;
    const EVENT_AFTER_RUNNING          = 101;
    const EVENT_BEFORE_ROUTING         = 102;
    const EVENT_AFTER_ROUTING          = 103;
    const EVENT_BEFORE_EXECUTING_ROUTE = 104;
    const EVENT_AFTER_EXECUTING_ROUTE  = 105;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var \Closure[]
     */
    protected $eventHandler = [];

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

    public function __construct($debug = false)
    {
        // set debug mode
        if (!is_bool($debug)) {
            $message = 'Parameter `debug` must be a boolean';
            throw new ParameterMustBeABooleanException($message);
        }
        $this->debug = $debug;

        // create config ans services
        $this->config   = new Config();
        $this->services = new Services();

        // register 404-event-hander
        $this->on(Hahns::EVENT_NOT_FOUND, function () {
            header('HTTP/1.1 404 Not Found');
        });

        // register 500-event-hander
        $this->on(Hahns::EVENT_ERROR, function (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');

            if ($this->debug) {
                $whoops = new Run();
                $whoops->pushHandler(new PrettyPageHandler());
                $whoops->register();
                throw $e;
            }
        });

        // register error_handler for throwing exceptions instead of trigger errors
        if ($this->debug) {
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                $e = new \ErrorException($errstr, $errno, 0, $errfile, $errline);
                $this->trigger(Hahns::EVENT_ERROR, [$e, $this]);
            });
        }

        $this->registerBuiltInParameters();
    }

    /**
     * @param string $prefix
     * @param string $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
     * @throws Exception\ParameterMustBeAStringException
     */
    protected function addPrefixedRoute($prefix, $route, $callbackOrNamedRoute, $name = null)
    {
        $route = sprintf('%s-%s', $prefix, $this->removeLastSlash($route));
        $this->router()->add($route, $callbackOrNamedRoute, $name);
    }

    /**
     * @param string          $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null     $name
     */
    public function any($route, $callbackOrNamedRoute, $name = null)
    {
        $this->delete($route, $callbackOrNamedRoute, $name);
        $this->get($route, $callbackOrNamedRoute, $name);
        $this->patch($route, $callbackOrNamedRoute, $name);
        $this->post($route, $callbackOrNamedRoute, $name);
        $this->put($route, $callbackOrNamedRoute, $name);
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return mixed|null
     */
    public function config($name, $value = null)
    {
        if (!is_null($value)) {
            $this->config->set($name, $value);
            return null;
        } else {
            return $this->config->get($name);
        }
    }

    /**
     * @param string $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
     */
    public function delete($route, $callbackOrNamedRoute, $name = null)
    {
        $this->addPrefixedRoute('delete', $route, $callbackOrNamedRoute, $name);
    }

    /**
     * @param string $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
     */
    public function get($route, $callbackOrNamedRoute, $name = null)
    {
        $this->addPrefixedRoute('get', $route, $callbackOrNamedRoute, $name);
    }

    /**
     * @param int $event
     * @param \Closure $callback
     * @throws Exception\ParameterMustBeAnIntegerException
     */
    public function on($event, \Closure $callback)
    {
        if (!is_int($event)) {
            $message = 'Parameter `event` must be an integer';
            throw new ParameterMustBeAnIntegerException($message);
        }

        if (!isset($this->eventHandler[$event])) {
            $this->eventHandler[$event] = [];
        }

        $this->eventHandler[$event][] = $callback;
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
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
     */
    public function patch($route, $callbackOrNamedRoute, $name = null)
    {
        $this->addPrefixedRoute('patch', $route, $callbackOrNamedRoute, $name);
    }

    /**
     * @param string $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
     */
    public function post($route, $callbackOrNamedRoute, $name = null)
    {
        $this->addPrefixedRoute('post', $route, $callbackOrNamedRoute, $name);
    }

    /**
     * @param string $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
     */
    public function put($route, $callbackOrNamedRoute, $name = null)
    {
        $this->addPrefixedRoute('put', $route, $callbackOrNamedRoute, $name);
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
    protected function router()
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
        $this->trigger(Hahns::EVENT_BEFORE_RUNNING, [$route, $this]);

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
        $usedRoute = $route;
        $route     = strtolower($_SERVER['REQUEST_METHOD']) . '-' . $route;

        try {
            $this->trigger(Hahns::EVENT_BEFORE_ROUTING, [$usedRoute, $this]);
            $this->router()->dispatch($route);
            $this->trigger(Hahns::EVENT_AFTER_ROUTING, [$usedRoute, $this]);

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
            $this->trigger(Hahns::EVENT_BEFORE_EXECUTING_ROUTE, [$usedRoute, $callback, $attributes, $this]);
            echo call_user_func_array($callback, $attributes);
            $this->trigger(Hahns::EVENT_AFTER_EXECUTING_ROUTE, [$usedRoute, $callback, $attributes, $this]);
        } catch (NotFoundException $e) {
            $this->trigger(Hahns::EVENT_NOT_FOUND, [$usedRoute, $this, $e]);
        } catch (\Exception $e) {
            $this->trigger(Hahns::EVENT_ERROR, [$e, $this]);
        }

        $this->trigger(Hahns::EVENT_AFTER_RUNNING, [$usedRoute, $this]);
    }

    /**
     * @param string $name
     * @param \Closure $callback
     * @return object|null
     */
    public function service($name, \Closure $callback = null)
    {
        if ($callback instanceof \Closure) {
            $this->services->register($name, $callback);
            return null;
        } else {
            return $this->services->get($name);
        }
    }

    protected function trigger($event, $args = [])
    {
        if (!is_int($event)) {
            $message = 'Parameter `event` must be an integer';
            throw new ParameterMustBeAnIntegerException($message);
        }

        if (isset($this->eventHandler[$event])) {
            foreach ($this->eventHandler[$event] as $handler) {
                call_user_func_array($handler, $args);
            }
        }
    }
}
