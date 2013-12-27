# Hahns [![Build Status](https://travis-ci.org/pklink/Hahns.png?branch=master)](https://travis-ci.org/pklink/Hahns) [![Dependency Status](https://www.versioneye.com/user/projects/52b89440ec1375c3f500001b/badge.png)](https://www.versioneye.com/user/projects/52b89440ec1375c3f500001b)

Hahns is a micro framework for PHP 5.4 and higher.

## Installation

To install using [composer][1], have the following lines in your `composer.json` file.

```json
{
  "require": {
    "pklink/hahns": "*",
  }
}
```

## Usage

Create application

```php
$app = new \Hahns\Hahns();
```


```php
$app->get('/', function () {
    return "hello world!";
});

$app->delete('/', function () {
    return "1";
});
```

Every GET-request to `/` will respond

```
hello world!
```

Every DELETE-request to `/` will respond

```
1
```

### Debug mode

For enable debugging pass `true` to the constructor of Hahns

```php
$app = new \Hahns\Hahns(true);
```

### Parameters for routing callback

Hahns will set parameters based on the required type automatically

The following types are built-in:

* `\Hahns\Hahns`
* `\Hahns\Config`
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

#### Add your own parameter

```
$app->parameter('\\stdClass', function() {
    $obj = new stdClass();
    $obj->test = 'yup';
    return $obj;
});

$app->get('/own/parameter', function (\stdClass $obj) {
    return $obj->test;
});

$app->parameter('\Package\MyOwnClass', function(\Hahns\Hahns $app) {
    // ...
});
```

The callback for `parameter()` must be return an instance of the given type.


### Named Parameters

Based on [regular expressions][2]

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

### Named Routes

```php
$app->get('/route1', function () {
    return 'hello world';
}, 'route1');
$app->get('/route2', 'route1', 'route2');
$app->get('/route3', 'route2');
```

All GET_request to `/route1`, `/route2` or `/route3` respond

```
hello world
```

### Services

```php
$app->service('myservice', function(\Hahns\Hahns $app) {
	$service = new \stdClass();
	$service->test    = 'hello';
	$service->appName = $app->config('name');
	return $service;
});

$app->get('/service-test', function (\Hahns\Hahns $app) {
	echo $app->service->test;
});
```

Every GET-request to `/service-test` will respond

```
hello
```

Built-in services are:

* `config` returns instance of `\Hahns\Config`
* `html-response` returns instance of `\Hahns\Response\Html`
* `json-response` returns instance of `\Hahns\Response\Json`
* `text-response` returns instance of `\Hahns\Response\Text`

### Events

Hahns trigger various events. Use the `on`-method to add your own handler.

#### Not Found (404)

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`
* `\Hahns\Exception\NotFoundException $e`

```php
$app->on(\Hahns\Hahns::EVENT_NOT_FOUND, function ($usedRoute, \Hahns\Hahns $app, \Hahns\Exception\NotFoundException $e) {
    // do something
});
```

Per default Hahns sends status code 404

##### Trigger a "Not Found" event

Simply throw a `\Hahns\Exception\NotFoundException`

```php
$app->get('/not-found', function () {
    throw new \Hahns\Exception\NotFoundException();
});
```


#### Error

Arguments are:

* `\Exception $e`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_ERROR, function (\Exception $e, \Hahns\Hahns $app) {
    // do something
});
```

Per default Hahns sends status code 500

##### Trigger an "Error" event

Simply throw a `\Hahns\Exception\ErrorException`

```php
$app->get('/not-found', function () {
    throw new \Hahns\Exception\NotFoundException();
});
```



#### Before Running

Arguments are:

* `string $givenRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_BEFORE_RUNNING, function ($givenRoute, \Hahns\Hahns $app) {
    // do something
});
```

#### After Running

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_AFTER_RUNNING, function ($usedRoute, \Hahns\Hahns $app) {
    // do something
});
```

#### Before Routing

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_BEFORE_ROUTING, function ($usedRoute, \Hahns\Hahns $app) {
    // do something
});
```

#### After Routing

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_AFTER_ROUTING, function ($usedRoute, \Hahns\Hahns $app) {
    // do something
});
```

#### Before execute matched route

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

#### After execute matched route

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

[1]: http://getcomposer.org/
[2]: http://en.wikipedia.org/wiki/Regular_expression