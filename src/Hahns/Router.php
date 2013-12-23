<?php


namespace Hahns;


use Hahns\Exception\CallbackDoesNotExistException;
use Hahns\Exception\ParameterMustBeAStringOrNullException;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Exception\RouteNotFoundException;

class Router
{

    /**
     * @var string
     */
    protected $parsable = '';

    /**
     * @var array
     */
    protected $namedParameters = [];

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @var array
     */
    protected $routes = [];

    public function __construct($parsable = null)
    {
        if (!is_string($parsable) && !is_null($parsable)) {
            $message = 'Parameter `parsable must be a string or null';
            throw new ParameterMustBeAStringOrNullException($message);
        }

        if ($parsable !== null) {
            $this->parsable = $this->removeLastSlash($parsable);
        } elseif (isset($_SERVER['PATH_INFO'])) {
            $this->parsable = $this->removeLastSlash($_SERVER['PATH_INFO']);
        } else {
            $this->parsable = '';
        }
    }

    /**
     * @param string $verb
     * @param string $route
     * @param \Closure $callback
     * @throws Exception\ParameterMustBeAStringException
     */
    public function add($verb, $route, \Closure $callback)
    {
        if (!is_string($verb)) {
            $message = 'Parameter `verb` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        if (!is_string($route)) {
            $message = 'Parameter `route` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        // lowercase $verb
        $verb = strtolower($verb);

        if (!isset($this->routes[$verb])) {
            $this->routes[$verb] = [];
        }

        $route                 = $this->removeLastSlash($route);
        $this->routes[$verb][] = [$route, $callback];
    }

    /**
     * @return \Closure
     * @throws Exception\CallbackDoesNotExistException
     */
    public function getCallback()
    {
        if (is_null($this->callback)) {
            throw new CallbackDoesNotExistException();
        }

        return $this->callback;
    }

    /**
     * @return array
     */
    public function getNamedParameters()
    {
        return $this->namedParameters;
    }

    /**
     * @return string
     */
    public function getParsable()
    {
        return $this->parsable;
    }

    /**
     * @param string $path
     * @return int
     */
    private function getPathDepth($path)
    {
        return count(explode('/', $path));
    }

    /**
     * @return array
     */
    private function getRouteCandidates()
    {
        $candidates = [];
        $pathDepthOfParsable = $this->getPathDepth($this->parsable);

        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (!isset($this->routes[$method])) {
            $routes = [];
        } else {
            $routes = $this->routes[$method];
        }

        foreach ($routes as $route) {
            $pathDepth = $this->getPathDepth($route[0]);

            if ($pathDepth != $pathDepthOfParsable) {
                continue;
            }

            $candidates[] = $route;
        }

        return $candidates;
    }

    /**
     * @throws Exception\RouteNotFoundException
     */
    public function dispatch()
    {
        // clear named parameters
        $this->namedParameters =[];

        // itereate routes
        foreach ($this->getRouteCandidates() as $route) {
            // get pattern and callback of parsable
            list($route, $callback) = $route;


            // split parsable and route
            $splittedRoute    = explode('/', $route);
            $splittedParsable = explode('/', $this->parsable);

            $paramPattern = '/\[(.+):(.+)\]/U';

            $namedParameter = [];
            foreach ($splittedRoute as $index => $routeSplit) {
                // leere element ueberspringen
                if (strlen($routeSplit) == 0) {
                    continue;
                }

                // pruefen ob element einen parameter enthaelt
                preg_match($paramPattern, $routeSplit, $match);

                // wenn parameter enthaelt muss dieser geparst werden
                if (count($match) > 0) {
                    // match aus route entfernen
                    $strpos = strpos($splittedRoute[$index], $match[0]);
                    $strlen = strlen($splittedRoute[$index]);
                    $splittedRoute[$index] = substr_replace($splittedRoute[$index], '', $strpos, $strlen);
                    if ($splittedRoute[$index] === false) {
                        $splittedRoute[$index] = null;
                    }

                    // regex des parameter aufarbeiten
                    $pattern = sprintf('/%s/', $match[1]);

                    // name des parameter speichern
                    $name    = $match[2];

                    // regex auf gleiches element von parsable ausfuehren
                    preg_match($pattern, $splittedParsable[$index], $match);

                    if (count($match) > 0) {
                        // match als wert speichern
                        $namedParameter[$name] = $match[0];

                        // match aus parsable entfernen
                        $strpos = strpos($splittedParsable[$index], $match[0]);
                        $strlen = strlen($splittedParsable[$index]);
                        $splittedParsable[$index] = substr_replace($splittedRoute[$index], '', $strpos, $strlen);
                        if ($splittedParsable[$index] === false) {
                            $splittedParsable[$index] = null;
                        }
                    }
                }
            }

            // route und parsable wieder zusammensetzen und vergleichen
            $route    = implode('/', $splittedRoute);
            $parsable = implode('/', $splittedParsable);

            if ($route !== $parsable) {
                continue;
            }

            // set named parameters and callback
            $this->namedParameters = $namedParameter;
            $this->callback        = $callback;

            return;
        }

        throw new RouteNotFoundException();
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
}
