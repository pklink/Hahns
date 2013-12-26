# Hahns [![Build Status](https://travis-ci.org/pklink/Hahns.png?branch=master)](https://travis-ci.org/pklink/Hahns) [![Dependency Status](https://www.versioneye.com/user/projects/52b89440ec1375c3f500001b/badge.png)](https://www.versioneye.com/user/projects/52b89440ec1375c3f500001b)

Hahns ist ein Micro-Web-Framework für PHP 5.4+.

## Installation

To install using [composer][1], have the following lines in your `composer.json` file.

```json
{
  "require": {
    "pklink/Hahns": "*",
  }
}
```

## Benutzung

Als Erstes benötigst du eine Instanz von *Hahns*

```php
$app = new \Hahns\Hahns();
```

Dann teilst du *Hahns* mit auf welchen Routen er zu reagieren hat


```php
$app->get('/', function () {
    return "hello world!";
});

$app->delete('/', function () {
    return "1";
});
```

Wie du siehst wird zwischen den verschiedenen HTTP-Verbs unterschieden. Jeder GET-Request auf `/` gibt nun also

```
hello world!
```

zurück und jeder DELETE-Request auf `/`

```
1
```

Momentan können folgende Verbs verarbeitet werden:

* GET
* POST
* PUT
* PATCH
* DELETE

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
\Hahns\Config   config()	                                                // get instance of \Hahns\Config
mixed           config(string $name)	                                    // get value $name from config
void            config(string $name, mixed $value)	                        // set value $value to config
void            delete(string $route, \Closure $callback)	                // register DELETE-route
void            delete(string $route, \Closure $callback, string $name)	    // register DELETE-route with name $name
void            get(string $route, \Closure $callback)		                // register GET-route
void            get(string $route, \Closure $callback, string $name)	    // register GET-route with name $name
void            on(int $event, \Closure $callback)                          // add handler $callback for event $event
void            parameter(string type, \Closure $callback)                  // register parameter for route callback
void            patch(string $route, \Closure $callback)	                // register PATCH-route
void            patch(string $route, \Closure $callback, string $name)	    // register PATCH-route with name $name
void            post(string $route, \Closure $callback)	                    // register POST-route
void            post(string $route, \Closure $callback, string $name)	    // register POST-route with name $name
void            put(string $route, \Closure $callback)		                // register PUT-route
void            put(string $route, \Closure $callback, string $name)		// register PUT-route with name $name
\Hahns\Router   router()                                                    // get instance of \Hanhs\Router
void            run()										                // start routing
void            service(string $name, \Closure $callback)	                // register service
\Hahns\Services services()	                                                // get all registered services
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
void   redirect(string $location, int $code)                    // send location header with status code $code
string send(string $data)	                                    // get $data as html
string send(string $data, array $header)	                    // get $data as html and send $header ['name' => 'value'] to to client
void   status(int code)                                         // send status code $code with HTTP version 1.1 to client
void   status(int code, string $message)                        // send status code $code with message $message to client
void   status(int code, string $message, string $httpVersion)   // send status code $code with message $message and HTTP version $version to client
```

### `\Hahns\Response\Json`
```
void   header(string $name, string $value)		                // send header $name with value $value
void   redirect(string $location)                               // send location header with status code 301
void   redirect(string $location, int $code)                    // send location header with status code $code
string send(string $data)	                                    // get $data as json-decoded string
string send(string $data, array $header)	                    // get $data as json-decoded string and send $header ['name' => 'value'] to to client
void   status(int code)                                         // send status code $code with HTTP version 1.1 to client
void   status(int code, string $message)                        // send status code $code with message $message to client
void   status(int code, string $message, string $httpVersion)   // send status code $code with message $message and HTTP version $version to client
```

### `\Hahns\Response\Text`
```
void   header(string $name, string $value)		                // send header $name with value $value
void   redirect(string $location)                               // send location header with status code 301
void   redirect(string $location, int $code)                    // send location header with status code $code
string send(string $data)	                                    // get $data as text
string send(string $data, array $header)	                    // get $data as text and send $header ['name' => 'value'] to to client
void   status(int code)                                         // send status code $code with HTTP version 1.1 to client
void   status(int code, string $message)                        // send status code $code with message $message to client
void   status(int code, string $message, string $httpVersion)   // send status code $code with message $message and HTTP version $version to client
```

### `\Hahns\Services`
```
object get(string $name)	// get service with name $name
```


[1]: http://getcomposer.org/
[2]: http://en.wikipedia.org/wiki/Regular_expression