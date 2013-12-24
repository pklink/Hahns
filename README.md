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

* `\Hahns\Request`
* `\Hahns\Response\Html`
* `\Hahns\Response\Json`
* `\Hahns\Response\Text`
* `\Hahns\ServiceHolder`

```php
$app->get('/', function (\Hahns\Request $request) {
    // ...
});

$app->patch('/', function (\Hahns\Response\Jsonl $response) {
    // ...
});

$app->post('/cars', function (\Hahns\ServiceHolder $services) {
    // ...
});

$app->get('/cars', function (\Hahns\ServiceHolder $services, \Hahns\Response\Jsonl $response, \Hahns\Request $request) {
    // ...
});

$app->get('/cars', function (\Hahns\Response\Jsonl $response, \Hahns\ServiceHolder $services) {
    // ...
});

$app->get('/cars', function (\Hahns\Response\Html $response) {
    // ...
});

$app->get('/cars', function (\Hahns\Response\Text $response) {
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
$app->get('/hello/[.+:name]', function (\Hahns\Response\Jsonl $response, \Hahns\Request $request) {
	return $response->send([
		'message' => sprintf('hello %s %s', $request->get('first'), $request->get('last'))
});

$app->get('/hello/[.+:first]/[.+:last]', function (\Hahns\Request $request, \Hahns\Response\Jsonl $response) {
	return $response->send([
		'message' => sprintf('hello %s %s', $request->get('first'), $request->get('last'))
	]);
});

$app->delete('/cars/id-[\d+:id]/now', function (\Hahns\Response\Jsonl $response, \Hahns\Request $request) {
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

$app->get('service-test', function (\Hahns\ServiceHolder $services) {
	echo $service->test;
});
```

Every GET-request to `/` will respond

```
hello
```

### 404-Handling

Default handling is sending a status code of `404`

Additionally you can add your own handler:

```php
$app->notFound(function() {
	// do something
});
```

## Reference

### `\Hahns\Hahns`
```
\Hahns\Hahns delete(string $route, \Closure $callback)	        // register DELETE-route
\Hahns\Hahns get(string $route, \Closure $callback)		        // register GET-route
\Hahns\Hahns notFound(\Closure $callback)				        // add handler for 404
\Hahns\Hahns void parameter(string type, \Closure $callback)    // register parameter for route callback
\Hahns\Hahns patch(string $route, \Closure $callback)	        // register PATCH-route
\Hahns\Hahns post(string $route, \Closure $callback)	        // register POST-route
\Hahns\Hahns put(string $route, \Closure $callback)		        // register PUT-route
void         run()										        // start routing
\Hahns\Hahns service(string $name, \Closure $callback)	        // register service
```

### `\Hahns\Request`
```
mixed get(string $name, mixed $default = null)		// get GET-param $name or $default
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

### `\Hahns\ServiceHolder`
```
object get(string $name)	// get service with name $name
```


[1]: http://getcomposer.org/
[2]: http://en.wikipedia.org/wiki/Regular_expression