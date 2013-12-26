<?php


namespace Hahns;


use Hahns\Exception\CallbackDoesNotExistException;
use Hahns\Exception\NotFoundException;
use Hahns\Exception\ParameterMustBeAClosureOrStringException;
use Hahns\Exception\ParameterMustBeAStringException;
use Hahns\Exception\ParameterMustBeAStringOrNullException;
use Hahns\Exception\RouteIsNotExistException;

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

    /**
     * @param string $route
     * @param \Closure $callback
     * @throws Exception\ParameterMustBeAStringException
     */

    /**
     * @param string $route
     * @param \Closure|string $callbackOrNamedRoute
     * @param string|null $name
     * @throws Exception\ParameterMustBeAStringException
     * @throws Exception\ParameterMustBeAStringOrNullException
     * @throws ParameterMustBeAClosureOrStringException
     */
    public function add($route, $callbackOrNamedRoute, $name = null)
    {
        if (!is_string($route)) {
            $message = 'Parameter `route` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        if (!is_null($name) && !is_string($name)) {
            $message = 'Parameter `name` must be a string or null';
            throw new ParameterMustBeAStringOrNullException($message);
        }

        // get callback
        $callback = null;
        if ($callbackOrNamedRoute instanceof \Closure) {
            $callback = $callbackOrNamedRoute;
        } elseif (is_string($callbackOrNamedRoute)) {
            $callback = $this->getRoute($callbackOrNamedRoute)[1];
        } else {
            $message = 'Parameter `callbackOrNamedRoute` must be a \\Closure or a string';
            throw new ParameterMustBeAClosureOrStringException($message);
        }

        if (!is_null($name)) {
            $index                = sprintf('named-%s', $name);
            $this->routes[$index] = [$route, $callback];
        } else {
            $this->routes[] = [$route, $callback];
        }
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
     * @param string $path
     * @return int
     */
    private function getPathDepth($path)
    {
        return count(explode('/', $path));
    }

    /**
     * @param string $name
     * @return array [route, callback]
     * @throws Exception\ParameterMustBeAStringException
     * @throws Exception\RouteIsNotExistException
     */
    public function getRoute($name)
    {
        if (!is_string($name)) {
            $message = 'Parameter `name` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        $index = sprintf('named-%s', $name);
        if (!isset($this->routes[$index])) {
            $message = sprintf('Route `%s` is not exist', $name);
            throw new RouteIsNotExistException($message);
        }

        return $this->routes[$index];
    }

    /**
     * @param string $parsable
     * @return array
     */
    private function getRouteCandidates($parsable)
    {
        $candidates = [];
        $pathDepthOfParsable = $this->getPathDepth($parsable);

        foreach ($this->routes as $route) {
            $pathDepth = $this->getPathDepth($route[0]);

            if ($pathDepth != $pathDepthOfParsable) {
                continue;
            }

            $candidates[] = $route;
        }

        return $candidates;
    }

    /**
     * @param string $parsable
     * @throws Exception\NotFoundException
     * @throws Exception\ParameterMustBeAStringException
     */
    public function dispatch($parsable)
    {
        if (!is_string($parsable)) {
            $message = 'Parameter `parseable` must be a string';
            throw new ParameterMustBeAStringException($message);
        }

        // clear named parameters
        $this->namedParameters =[];

        // itereate routes
        $callback = null;
        foreach ($this->getRouteCandidates($parsable) as $route) {
            // get pattern and callback of parsable
            list($route, $callback) = $route;

            // split parsable and route
            $splittedRoute    = explode('/', $route);
            $splittedParsable = explode('/', $parsable);

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
            $strippedRoute    = implode('/', $splittedRoute);
            $strippedParsable = implode('/', $splittedParsable);

            if ($strippedRoute != $strippedParsable) {
                continue;
            }

            // set named parameters and callback
            $this->namedParameters = $namedParameter;
            $this->callback        = $callback;

            return;
        }

        throw new NotFoundException();
    }
}
