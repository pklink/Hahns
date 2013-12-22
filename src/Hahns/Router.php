<?php


namespace Hahns;


class Router
{

    /**
     * @var string
     */
    protected $parsable = '';

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var string
     */
    private $namedParameterPattern = '/\[(.+):(.+)\]/U';

    public function __construct($parsable = null)
    {
        if ($parsable !== null) {
            $this->parsable = $this->removeLastSlash($parsable);
        } elseif (isset($_SERVER['PATH_INFO'])) {
            $this->parsable = $this->removeLastSlash($_SERVER['PATH_INFO']);
        } else {
            $this->parsable = '';
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
     * @param string $route
     * @param $callback
     */
    public function get($route, $callback)
    {
        $route          = $this->removeLastSlash($route);
        $this->routes[] = [$route, $callback];
    }

    /**
     * @return bool
     */
    public function dispatch()
    {
        // itereate routes
        foreach ($this->getRouteCandidates() as $route) {
            // get pattern and callback of parsable
            list($route, $callback) = $route;

            // get named parameter
            $namedParameter = $this->getNamedParameter($route);

            // if named parameter are not parsable, then continue wirh the next route
            if ($namedParameter === false) {
                continue;
            }

            // call callback
            call_user_func($callback, $namedParameter);

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    private function getRouteCandidates()
    {
        $candidates = [];
        $pathDepthOfParsable = $this->getPathDepth($this->parsable);

        foreach ($this->routes as $route) {
            $pathDepth = $this->getPathDepth($route[0]);

            if ($pathDepth != $pathDepthOfParsable) {
                continue;
            }

            if ($this->compareWithoutNamedParameter($route[0]) === false) {
                continue;
            }

            $candidates[] = $route;
        }

        return $candidates;
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
     * @param string $route
     * @return bool
     */
    private function compareWithoutNamedParameter($route)
    {
        $parsable          = $this->parsable;
        $splittedRoute     = explode('/', $route);
        $splittedParseable = explode('/', $parsable);

        preg_match_all($this->namedParameterPattern, $route, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $match = $match[0];

            foreach ($splittedRoute as $index => $split) {
                if ($split == $match) {
                    unset($splittedRoute[$index]);
                    unset($splittedParseable[$index]);
                    break;
                }
            }
        }

        $parsable = implode('/', $splittedParseable);
        $route    = implode('/', $splittedRoute);

        return $parsable == $route;
    }

    /**
     * @param string $route
     * @return array|false
     */
    private function getNamedParameter($route)
    {
        // search the named parameter
        preg_match_all($this->namedParameterPattern, $route, $matches, PREG_SET_ORDER);

        // parse the named parameter
        $startPos       = 0;
        $namedParameter = [];
        foreach ($matches as $match) {
            if (is_array($matches)) {
                // der named parameter
                $pattern = $match[0];

                // regex des named parameter
                $paramPattern = sprintf('/%s/', $match[1]);

                // name des named paramter
                $name = $match[2];

                // position ermitteln an der gematched wird
                if ($startPos == 0) {
                    $startPos = strpos($route, $pattern);
                }

                // alles vor dem match abschneiden
                $subject = substr($this->parsable, $startPos);

                // evt. query string noch auf das naechste '/' einschranken
                $endPos = strpos($subject, '/');

                if ($endPos !== false) {
                    $subject = substr($subject, 0, $endPos);
                    $startPos    += 1;
                }

                // pos um die laenge des suchbereichs verschieben
                $startPos += strlen($subject);

                // nach dem wert des named parameter suche
                $value = $this->matchRegex($paramPattern, $subject);

                if ($value === false) {
                    return false;
                }

                $namedParameter[$name] = $value;
            }
        }

        return $namedParameter;
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @return string|false
     */
    private function matchRegex($pattern, $subject)
    {
        $matches = [];
        preg_match($pattern, $subject, $matches);

        if (count($matches) == 0) {
            return false;
        } else {
            return $matches[0];
        }
    }
}
