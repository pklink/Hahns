<?php


namespace Hahns;


use Dotor\Dotor;
use Hahns\Exception\NotFoundException;
use Hahns\Exception\VariableHasToBeABooleanException;
use Hahns\Exception\VariableHasToBeAnIntegerException;
use Hahns\Exception\VariableHasToBeAStringException;
use Hahns\Exception\VariableHasToBeAStringOrNullException;
use Hahns\Response\Html;
use Hahns\Response\Json;
use Hahns\Response\Text;
use Hahns\Validator\ArrayValidator;
use Hahns\Validator\BooleanValidator;
use Hahns\Validator\CallableValidator;
use Hahns\Validator\IntegerValidator;
use Hahns\Validator\StringValidator;
use WebDriver\Exception;

class Hahns
{

    const EVENT_NOT_FOUND = 404;
    const EVENT_ERROR = 500;
    const EVENT_BEFORE_RUNNING = 100;
    const EVENT_AFTER_RUNNING = 101;
    const EVENT_BEFORE_ROUTING = 102;
    const EVENT_AFTER_ROUTING = 103;
    const EVENT_BEFORE_EXECUTING_ROUTE = 104;
    const EVENT_AFTER_EXECUTING_ROUTE = 105;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var \Closure[][]
     */
    protected $eventHandler = [];

    /**
     * @var ParameterHolder
     */
    protected $parameterHolder = [];

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ServiceHolder
     */
    protected $serviceHolder;

    /**
     * @param array $config
     * @throws VariableHasToBeABooleanException
     */
    public function __construct($config = [])
    {
        // validate config
        ArrayValidator::hasTo($config, 'debug');

        // create config
        $this->config = new Dotor($config);

        // set debug mode
        $this->debug = $this->config->getBool('debug', false);

        // create service holder and parameter holder
        $this->serviceHolder = new ServiceHolder();
        $this->parameterHolder = new ParameterHolder();

        $this->registerBuiltInEventHandler();
        $this->registerErrorHandler();
        $this->registerBuiltInServices();
        $this->registerBuiltInParameters();
    }

    /**
     * @param string $prefix
     * @param string $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
     * @throws VariableHasToBeAStringException
     */
    protected function addPrefixedRoute($prefix, $route, $callbackOrNamedRoute, $name = null)
    {
        $route = sprintf('%s-%s', $prefix, $this->removeLastSlash($route));
        $this->router()->add($route, $callbackOrNamedRoute, $name);
    }

    /**
     * @param string $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
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
     * @return mixed|null
     */
    public function config($name)
    {
        return $this->config->get($name);
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
     * @throws VariableHasToBeAnIntegerException
     */
    public function on($event, \Closure $callback)
    {
        IntegerValidator::hasTo($event, 'event');

        // check if event is exists
        if (!isset($this->eventHandler[$event])) {
            $this->eventHandler[$event] = [];
        }

        $this->eventHandler[$event][] = $callback;
    }

    /**
     * @param string $type
     * @param \Closure $callback
     * @param boolean $asSingleton
     */
    public function parameter($type, \Closure $callback, $asSingleton = true)
    {
        $this->parameterHolder->register($type, $callback, $asSingleton, [$this]);
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
     * @return void
     */
    protected function registerBuiltInEventHandler()
    {
        // register 404-event-hander
        $this->on(Hahns::EVENT_NOT_FOUND, function () {
            $this->service('json-response')->status(404);
        });

        $this->on(Hahns::EVENT_ERROR, function () {
            $this->service('text-response')->status(500);
        });
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

        $this->parameter('Hahns\\Response\\Json', function () {
            return $this->service('json-response');
        });

        $this->parameter('Hahns\\Response\\Text', function () {
            return $this->service('text-response');
        });

        $this->parameter('Hahns\\Response\\Html', function () {
            return $this->service('html-response');
        });
    }

    /**
     * @return void
     */
    protected function registerBuiltInServices()
    {
        $this->serviceHolder->register('json-response', function () {
            return new Json();
        });

        $this->serviceHolder->register('text-response', function () {
            return new Text();
        });

        $this->serviceHolder->register('html-response', function () {
            return new Html();
        });
    }

    /**
     * @return void
     */
    protected function registerErrorHandler()
    {
        // register error_handler for throwing exceptions instead of trigger errors
        if ($this->debug) {
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                $e = new \ErrorException($errstr, $errno, 0, $errfile, $errline);
                $this->trigger(Hahns::EVENT_ERROR, [$e, $this]);
            });
        }
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
     * @throws VariableHasToBeAStringOrNullException
     */
    public function run($route = null, $requestMethod = null)
    {
        $this->trigger(Hahns::EVENT_BEFORE_RUNNING, [$route, $this]);

        StringValidator::hasToBeStringOrNull($route, 'route');
        StringValidator::hasToBeStringOrNull($requestMethod, 'requestMethod');

        // get route
        if ($route !== null) {
            $route = $this->removeLastSlash($route);
        } elseif (isset($_SERVER['PATH_INFO'])) {
            $route = $this->removeLastSlash($_SERVER['PATH_INFO']);
        } else {
            $route = '';
        }

        // save used route
        $usedRoute = $route;

        // get request method
        if (is_null($requestMethod)) {
            $requestMethod = $_SERVER['REQUEST_METHOD'];
        }

        // get method and concat with $route
        $route = strtolower($requestMethod) . '-' . $route;

        try {
            $this->trigger(Hahns::EVENT_BEFORE_ROUTING, [$usedRoute, $this]);
            $this->router()->dispatch($route);
            $this->trigger(Hahns::EVENT_AFTER_ROUTING, [$usedRoute, $this]);

            // get callback
            $callback = $this->router()->getCallback();

            // get attributes for callback
            $attributes = [];
            $callbackReflection = new \ReflectionFunction($callback);

            foreach ($callbackReflection->getParameters() as $parameter) {
                $type = $parameter->getClass()->getName();
                $attributes[] = $this->parameterHolder->get($type);
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
            $this->serviceHolder->register($name, $callback, [$this]);
            return null;
        } else {
            return $this->serviceHolder->get($name);
        }
    }

    /**
     * @param int $handler
     * @param array $args
     * @throws VariableHasToBeAnIntegerException
     * @throws \Exception
     */
    protected function trigger($handler, $args = [])
    {
        IntegerValidator::hasTo($handler, 'handler');

        if (isset($this->eventHandler[$handler])) {
            $eventHandler = $this->eventHandler[$handler];

            ArrayValidator::hasTo($eventHandler, 'eventHandler');

            foreach ($eventHandler as $handler) {
                CallableValidator::hasTo($handler, 'handler');
                call_user_func_array($handler, $args);
            }
        }
    }
}
