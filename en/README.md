# Dokumentation

*version 0.1 based on Hahns 0.7.1*

Hahns is a lightweight Micro-Web-Framework for PHP 5.4+.

## Installation

To install using [composer](http://getcomposer.org/), have the following lines in your `composer.json` file.

```json
{
  "require": {
    "pklink/Hahns": "*",
  }
}
```

## Hello World!

Yip yip! Hello World! Here are a few implementation examples: 

```php
$app = new \Hahns\Hahns();
$app->get('/', function () {
    return 'Hello World!';
});
$app->run();
```

or

```php
$app = new \Hahns\Hahns();
$app->get('/', function (\Hahns\Response\Html $response) {
    return $response->send('<h1>Hello World!</h1>');
});
$app->run();
```

or

```php
$app = new \Hahns\Hahns();
$app->get('/', function (\Hahns\Hahns $app) {
    return $app->service('text-response')->send('Hello World!');
});
$app->run();
```

## Routing

Using routing, you bind certain requests with your very own functions, which return a string and get sent to the client - probably a browser.

For example, if you want a request to `index.php/hello` to return a  pleasant  `Hello!`, you could solve it like this:

```php
$app->get('/hello', function () {
    return 'Hello!';
});
```

You just registered the route `/hello`. If this route gets called in a browser, Hahns executes the callback, which in turn emits the pleasant greeting:


```
Hello!
```

In this special case, only GET-requests will be handled. If you want to respond to POST-requests only, use the `post()`-method. Same goes for DELETE-requests, just use `delete()`.


### Named Parameter

Based on regular expressions

```php
$app->get('/hello/[.+:name]', function (\Hahns\Response\Json $response, \Hahns\Request $request) {
	return $response->send([
		'message' => sprintf('hello %s %s', $request->get('first'), $request->get('last'))
});

$app->get('/hello/[.+:first]/[.+:last]', function (\Hahns\Request $request, \Hahns\Response\Json $response) {
	return $response->send([
		'message' => sprintf('hello %s %s', $request->get('first'), $request->get('last'))
	]);
});

$app->delete('/cars/id-[\d+:id]/now', function (\Hahns\Response\Json $response, \Hahns\Request $request) {
    return $response->send([
        'message' => sprintf('removed card with id `%d`', $request->get('id'))
    ]);
});
```

### Parameters for Callbacks

You can use arbitrary parameters for the callback of a route - *Hahns* applies them automatically. He hereby checks, which parameters are expected, and applies them accordingly. Therefore, it is inevitable that the parameters are typecasted.

You can now use parameters, which are registered beforehand (see below). Pre-defined are:

* `\Hahns\Hahns`
* `\Hahns\Request`
* `\Hahns\Response\Html`
* `\Hahns\Response\Json`
* `\Hahns\Response\Text`

```php
$app->get('/', function (\Hahns\Request $request) {
    // ...
});

$app->patch('/', function (\Hahns\Response\Json $response) {
    // ...
});

$app->get('/cars', function (\Hahns\Response\Json $response, \Hahns\Request $request) {
    // ...
});
```

#### Create your own Routing-Parameter

Besides having to be an object, there are no special conditions bound to parameters. You can register any new types with the `parameter()`-method. This method expects the type of the parameter as the first and a callback, in which the object gets instanced and returned as the second argument:

```php
$app->parameter('\\stdClass', function() {
    $obj = new stdClass();
    $obj->test = 'yup';
    return $obj;
});

$app->get('/own/parameter', function (\stdClass $obj) {
    return $obj->test;
});
```

Optionally, you can use the instance of `\Hahns\Hahns` in your callback. It is sufficent to set the corresponding parameter:


```php
$app->parameter('\\stdClass', function(\Hahns\Hahns $app) {
    // ...
});
```

In default, parameters are handled as singleton. This means that the callback gets invoced once, the returned object saved and reused. You want to the callback to get invoced again - in order to rebuild the parameter - on each use? Just assign `false` als a third argument to the `parameter()`-method.

```php
$app->parameter('\\stdClass' function() {
    // ....
}, false);
```




## Services

Services are named objects, generated once and available via *Hahns* at all time. To use a service, there's *Hahns* `service()`-method.


```php
$app->service('service-name');
```

In default, the following services are available:

* `html-response` delivers an instance of  `\Hahns\Response\Html`
* `json-response` delivers an instance of  `\Hahns\Response\Json`
* `text-response` delivers an instance of  `\Hahns\Response\Text`


### Create your own service

Services are the ideal way to use third-party-libraries within your application - for example [Twig](http://twig.sensiolabs.org/) as a template engine or [Propel](http://propelorm.org/) as an ORM.

To create a service, you use *Hahns* `service()`-method likewise:

```php
$app->service('myservice', function() {
    $service = new \stdClass();
    $service->test = 'hello';
    return $service;
});
```

The name you'll use to access the service later goes as the first argument. Just deliver a callback, which creates and returns the service, as the second argument.
The best thing about services is that the callback gets executed only once - you can do all your configuration tasks without having to worry about them getting executed multiple times.




Optionally, you can use the instance of `\Hahns\Hahns` in your callback. It is sufficent to set the corresponding parameter:

```php
$app->service('myservice', function(\Hahns\Hahns $app) {
    // ...
});
```

## Events

Hahns triggers various events. Use the `on`-method to add your own handler.

### Not Found (404)

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`
* `\Hahns\Exception\NotFoundException $e`

```php
$app->on(\Hahns\Hahns::EVENT_NOT_FOUND, function ($usedRoute, \Hahns\Hahns $app, \Hahns\Exception\NotFoundException $e) {
    // do something
});
```

In default, Hahns sends status code 404

#### Trigger a "Not Found" event

Simply throw a `\Hahns\Exception\NotFoundException`

```php
$app->get('/not-found', function () {
    throw new \Hahns\Exception\NotFoundException();
});
```


### Error

Arguments are:

* `\Exception $e`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_ERROR, function (\Exception $e, \Hahns\Hahns $app) {
    // do something
});
```

In default Hahns sends status code 500

#### Trigger an "Error" event

Simply throw a `\Hahns\Exception\ErrorException`

```php
$app->get('/not-found', function () {
    throw new \Hahns\Exception\NotFoundException();
});
```



### Before Running

Arguments are:

* `string $givenRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_BEFORE_RUNNING, function ($givenRoute, \Hahns\Hahns $app) {
    // do something
});
```

### After Running

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_AFTER_RUNNING, function ($usedRoute, \Hahns\Hahns $app) {
    // do something
});
```

### Before Routing

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_BEFORE_ROUTING, function ($usedRoute, \Hahns\Hahns $app) {
    // do something
});
```

### After Routing

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_AFTER_ROUTING, function ($usedRoute, \Hahns\Hahns $app) {
    // do something
});
```

### Before execute matched route

Arguments are:

* `string $usedRoute`
* `\Closure $routeCallback`
* `array $argsForCallback`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_BEFORE_EXECUTING_ROUTE, function ($usedRoute, \Closure $routeCallback, $argsForCallback, \Hahns\Hahns $app)
    // do something
});
```

### After execute matched route

Arguments are:

* `string $usedRoute`
* `\Closure $routeCallback`
* `array $argsForCallback`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_AFTER_EXECUTING_ROUTE, function ($usedRoute, \Closure $routeCallback, $argsForCallback, \Hahns\Hahns $app)
    // do something
});
```

## Reference

### `\Hahns\Hahns`
```
void            any(string $route, \Closure $callback)	                        // register route for all verbs
void            any(string $route, string $namedRoute)	                        // register route for all verbs using the previous route named $namedRoute
void            any(string $route, \Closure $callback, string $name)	        // register routes for all verbs route with name $name
void            any(string $route, string $namedRoute, string $name)	        // register route for all verbs with name $name using the previous route named $namedRoute
mixed           config(string $name)	                                        // get value $name from config
void            config(string $name, mixed $value)	                            // set value $value to config
void            delete(string $route, \Closure $callback)	                    // register DELETE-route
void            delete(string $route, string $namedRoute)	                    // register DELETE-route using the previous route $namedRoute
void            delete(string $route, \Closure $callback, string $name)	        // register DELETE-route with name $name
void            delete(string $route, string $namedRoute, string $name)	        // register DELETE-route with name $name using the previous route named $namedRoute
void            get(string $route, \Closure $callback)		                    // register GET-route
void            get(string $route, string $namedRoute)	                        // register GET-route using the previous route $namedRoute
void            get(string $route, \Closure $callback, string $name)	        // register GET-route with name $name
void            get(string $route, string $namedRoute, string $name)	        // register GET-route with name $name using the previous route named $namedRoute
void            on(int $event, \Closure $callback)                              // add handler $callback for event $event
void            parameter(string type, \Closure $callback)                      // register parameter for route callback as singleton
void            parameter(string type, \Closure $callback, bool $asSingleton)   // register parameter for route callback
void            patch(string $route, \Closure $callback)	                    // register PATCH-route
void            patch(string $route, string $namedRoute)	                    // register PATCH-route using the previous route $namedRoute
void            patch(string $route, \Closure $callback, string $name)	        // register PATCH-route with name $name
void            patch(string $route, string $namedRoute, string $name)	        // register PATCH-route with name $name using the previous route named $namedRoute
void            post(string $route, \Closure $callback)	                        // register POST-route
void            post(string $route, string $namedRoute)	                        // register POST-route using the previous route $namedRoute
void            post(string $route, \Closure $callback, string $name)	        // register POST-route with name $name
void            post(string $route, string $namedRoute, string $name)	        // register POST-route with name $name using the previous route named $namedRoute
void            put(string $route, \Closure $callback)		                    // register PUT-route
void            put(string $route, string $namedRoute)	                        // register PUT-route using the previous route $namedRoute
void            put(string $route, \Closure $callback, string $name)		    // register PUT-route with name $name
void            put(string $route, string $namedRoute, string $name)	        // register PUT-route with name $name using the previous route named $namedRoute
void            run()										                    // start routing
void            run(string $route)                                              // start routing with the given route $route
void            run(string $route, string $requestMethod)                       // start routing with the given route $route and the request method $requestMethod (useful for simulating request)
object          service(string $name)	                                        // get service with name $name
void            service(string $name, \Closure $callback)	                    // register service
```

### `\Hahns\Request`
```
mixed get(string $name)		                    // get GET-param $name or null
mixed get(string $name, mixed $default)		    // get GET-param $name or $default
mixed header(string $name)	                    // get param $name from request header or null
mixed header(string $name, mixed $default)	    // get param $name from request header or $default
mixed payload(string $name)	                    // get param $name from payload (DELETE, PATCH, PUT) or null
mixed payload(string $name, mixed $default)     // get param $name from payload (DELETE, PATCH, PUT) or $default
mixed post(string $name)		                // get POST-param $name or null
mixed post(string $name, mixed $default)		// get POST-param $name or $default
```

### `\Hahns\Response\Html`
```
void   header(string $name, string $value)		                // send header $name with value $value
void   redirect(string $location)                               // send location header with status code 301
void   redirect(string $location, int $status)                  // send location header with status code $code
string send(string $data)	                                    // get $data as html
string send(string $data, int $status)	                        // get $data as html and send status code $status to client
void   status(int code)                                         // send status code $code with HTTP version 1.1 to client
void   status(int code, string $message)                        // send status code $code with message $message to client
void   status(int code, string $message, string $httpVersion)   // send status code $code with message $message and HTTP version $version to client
```

### `\Hahns\Response\Json`
```
void   header(string $name, string $value)		                // send header $name with value $value
void   redirect(string $location)                               // send location header with status code 301
void   redirect(string $location, int $status)                  // send location header with status code $code
string send(string $data)	                                    // get $data as json-decoded string
string send(string $data, int $status)	                        // get $data as json-decoded string and send status code $status to client
void   status(int code)                                         // send status code $code with HTTP version 1.1 to client
void   status(int code, string $message)                        // send status code $code with message $message to client
void   status(int code, string $message, string $httpVersion)   // send status code $code with message $message and HTTP version $version to client
```

### `\Hahns\Response\Text`
```
void   header(string $name, string $value)		                // send header $name with value $value
void   redirect(string $location)                               // send location header with status code 301
void   redirect(string $location, int $status)                  // send location header with status code $code
string send(string $data)	                                    // get $data as text
string send(string $data, int $status)	                        // get $data as text and send status code $status to client
void   status(int code)                                         // send status code $code with HTTP version 1.1 to client
void   status(int code, string $message)                        // send status code $code with message $message to client
void   status(int code, string $message, string $httpVersion)   // send status code $code with message $message and HTTP version $version to client
```