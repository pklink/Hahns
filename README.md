# Hahns [![Build Status](https://travis-ci.org/pklink/Hahns.png?branch=master)](https://travis-ci.org/pklink/Hahns) [![Dependency Status](https://www.versioneye.com/user/projects/52b89440ec1375c3f500001b/badge.png)](https://www.versioneye.com/user/projects/52b89440ec1375c3f500001b)

Hahns is a micro framework for PHP 5.4 and higher.

## Installation

To install using [composer][1], have the following lines in your `composer.json` file.

```json
{
  "require": {
    "pklink/Hahns": "*",
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

### Parameters for routing callback

Hahns will set parameters based on the required type automatically

The following types are built-in:

* `\Hahns\Hahns`
* `\Hahns\Config`
* `\Hahns\Request`
* `\Hahns\Response\Html`
* `\Hahns\Response\Json`
* `\Hahns\Response\Text`
* `\Hahns\Services`

```php
$app->get('/', function (\Hahns\Request $request) {
    // ...
});

$app->patch('/', function (\Hahns\Response\Json $response) {
    // ...
});

$app->post('/cars', function (\Hahns\Services $services) {
    // ...
});

$app->get('/cars', function (\Hahns\Response\Json $response, \Hahns\Services $services) {
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

### Services

```php
$app->service('myservice', function() {
	$service = new \stdClass();
	$service->test = 'hello';
	return $service;
});

$app->get('/service-test', function (\Hahns\Services $services) {
	echo $service->test;
});
```

Every GET-request to `/service-test` will respond

```
hello
```

### Events

Hahns trigger various events. Use the `on`-method to add your own handler.

#### Not Found (404)

Arguments are:

* `string $usedRoute`
* `\Hahns\Hahns $app`

```php
$app->on(\Hahns\Hahns::EVENT_NOT_FOUND, function ($usedRoute, \Hahns\Hahns $app) {
    // do something
});
```

##### Trigger a "Not Found" event

Simply throw a `\Hahns\Exception\NotFoundException`

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
\Hahns\Config   config()	                                    // get instance of \Hahns\Config
mixed           config(string $name)	                        // get value $name from config
void            config(string $name, mixed $value)	            // set value $value to config
void            delete(string $route, \Closure $callback)	    // register DELETE-route
void            get(string $route, \Closure $callback)		    // register GET-route
void            on(int $event, \Closure $callback)              // add handler $callback for event $event
void            parameter(string type, \Closure $callback)      // register parameter for route callback
void            patch(string $route, \Closure $callback)	    // register PATCH-route
void            post(string $route, \Closure $callback)	        // register POST-route
void            put(string $route, \Closure $callback)		    // register PUT-route
\Hahns\Router   router()                                        // get instance of \Hanhs\Router
void            run()										    // start routing
void            service(string $name, \Closure $callback)	    // register service
\Hahns\Services services()	                                    // register service
```

### `\Hahns\Request`
```
mixed get(string $name, mixed $default = null)		// get GET-param $name or $default
mixed header(string $name, mixed $default = null)	// get param $name from request header
mixed payload(string $name, mixed $default = null)	// get param $name from payload (DELETE, PATCH, PUT) or $default
mixed post(string $name, mixed $default = null)		// get POST-param $name or $default
```

### `\Hahns\Response\Html`
```
void   header(string $name, string $value)		                                // send header $name with value $value
void   redirect(string $location, int $code = 301)                              // send location header
string send(string $data, array $header = [])	                                // get $data as html
void   status(int code, string $message = null, string $httpVersion = '1.1')    // send given status code to client
```

### `\Hahns\Response\Json`
```
void   header(string $name, string $value)		                                // send header $name with value $value
void   redirect(string $location, int $code = 301)                              // send location header
string send(array|object $data, array $header = [])	                            // get $data as json-decoded string
void   status(int code, string $message = null, string $httpVersion = '1.1')    // send given status code to client
```

### `\Hahns\Response\Text`
```
void   header(string $name, string $value)		                                // send header $name with value $value
void   redirect(string $location, int $code = 301)                              // send location header
string send(array|object $data, array $header = [])	                            // get $data as text
void   status(int code, string $message = null, string $httpVersion = '1.1')    // send given status code to client
```

### `\Hahns\Services`
```
object get(string $name)	// get service with name $name
```


[1]: http://getcomposer.org/
[2]: http://en.wikipedia.org/wiki/Regular_expression