# Hahns

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

Hahns will set parameters based on required type automatically

The following types are available:

* `\Hahns\Request`
* `\Hahns\Response\JsonImpl`
* `\Hahns\ServiceHolder`

```php
$app->get('/', function (\Hahns\Request $request) {
    // ...
});

$app->patch('/', function (\Hahns\Response\JsonImpl $response) {
    // ...
});

$app->post('/cars', function (\Hahns\ServiceHolder $services) {
    // ...
});

$app->get('/cars', function (\Hahns\ServiceHolder $services, \Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    // ...
});

$app->get('/cars', function (\Hahns\Response\JsonImpl $response, \Hahns\ServiceHolder $services) {
    // ...
});
```


### Named Parameters

Based on [regular expression][2]

```php
$app->get('/hello/[.+:name]', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
	return $response->send([
		'message' => sprintf('hello %s %s', $request->get('first'), $request->get('last'))
});

$app->get('/hello/[.+:first]/[.+:last]', function (\Hahns\Request $request, \Hahns\Response\JsonImpl $response) {
	return $response->send([
		'message' => sprintf('hello %s %s', $request->get('first'), $request->get('last'))
	]);
});

$app->delete('/cars/id-[\d+:id]/now', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
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

Additionally you can add own handler:

```php
$app->notFound(function() {
	// do something
});
```

## Reference

### `\Hahns\Hahns`

```
void delete(string $route, \Closure $callback)	// register DELETE-route
void get(string $route, \Closure $callback)		// register GET-route
void notFound(\Closure $callback)				// add handler for 404
void patch(string $route, \Closure $callback)	// register PATCH-route
void post(string $route, \Closure $callback)	// register POST-route
void put(string $route, \Closure $callback)		// register PUT-route
void run()										// start routing
void service(string $name, \Closure $callback)	// register service
```

### `\Hahns\Request`

It is usable as parameter for routing callbacks

```
mixed get(string $name, mixed $default = null)		// get GET-param $name or $default
mixed payload(string $name, mixed $default = null)	// get param $name from payload (DELETE, PATCH, PUT) or $default
mixed post(string $name, mixed $default = null)		// get POST-param $name or $default
```

### `\Hahns\Response\JsonImpl`

It is usable as parameter for routing callbacks

```
void   header(string $name, string $value)		// send header $name with value $value
string send(mixed $data, array $header = [])	// get $data as json-decoded string
```


### `\Hahns\ServiceHolder`

It is usable as parameter for routing callbacks

```
object get(string $name)	// get service with name $name
```

[1]: http://getcomposer.org/
[2]: http://en.wikipedia.org/wiki/Regular_expression