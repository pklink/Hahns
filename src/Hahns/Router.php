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

    function __construct($parsable = null)
    {
        if ($parsable !== null)
        {
            $this->parsable = $parsable;
        }

        elseif (isset($_SERVER['PATH_INFO']))
        {
            $this->parsable = $_SERVER['PATH_INFO'];
        }
    }

    /**
     * @param string $route
     * @param $callback
     */
    public function get($route, $callback)
    {
        $this->routes[] = [$route, $callback];
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

        if (count($matches) == 0)
        {
            return false;
        }
        else
        {
            return $matches[0];
        }
    }

    /**
     * @param string $path
     * @return int
     */
    private function getPathDepth($path)
    {
        // remove last '/'
        if ($path{strlen($path)-1} == '/')
        {
            $path = substr($path, 0, -1);
        }

        return count(explode('/', $path));
    }

    /**
     * @param string $route
     * @return array|false
     */
    private function getNamedParameter($route)
    {
        // search the named parameter
        $pattern = '/\[(.+):(.+)\]/U';
        preg_match_all($pattern, $route, $matches, PREG_SET_ORDER);

        // parse the named parameter
        $startPos       = 0;
        $namedParameter = [];
        foreach ($matches as $match)
        {
            if (is_array($matches))
            {
                // der named parameter
                $pattern = $match[0];

                // regex des named parameter
                $paramPattern = sprintf('/%s/', $match[1]);

                // name des named paramter
                $name = $match[2];

                // position ermitteln an der gematched wird
                if ($startPos == 0)
                {
                    $startPos = strpos($route, $pattern);
                }

                // alles vor dem match abschneiden
                $subject = substr($this->parsable, $startPos);

                // evt. query string noch auf das naechste '/' einschranken
                $endPos = strpos($subject, '/');

                if ($endPos !== false)
                {
                    $subject = substr($subject, 0, $endPos);
                    $startPos    += 1;
                }

                // pos um die laenge des suchbereichs verschieben
                $startPos += strlen($subject);

                // nach dem wert des named parameter suche
                $value = $this->matchRegex($paramPattern, $subject);

                if ($value === false)
                {
                    return false;
                }

                $namedParameter[$name] = $value;
            }
        }

        return $namedParameter;
    }

    /**
     * @return bool
     */
    public function dispatch()
    {
        // pfadtiefe des querystrings ermitteln
        $pathDepthOfQueryString = $this->getPathDepth($this->parsable);

        // itereate routes
        foreach ($this->routes as $route)
        {
            // get pattern and callback of parsable
            list($route, $callback) = $route;

            // get depth of route
            $pathDepthOfRoute = $this->getPathDepth($route);

            // if depth of parsable not equal depth of route, then continue with the next route
            if ($pathDepthOfQueryString != $pathDepthOfRoute)
            {
                continue;
            }

            // get named parameter
            $namedParameter = $this->getNamedParameter($route);

            // if named parameter are not parsable, then continue wirh the next route
            if ($namedParameter === false)
            {
                continue;
            }

            // call callback
            call_user_func($callback, $namedParameter);

            return true;
        }

        return false;
    }

}