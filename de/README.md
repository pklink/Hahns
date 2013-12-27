# Dokumentation

*version 0.1.6 basierend auf Hahns 0.7.1*

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

Dann teilst du *Hahns* mit auf welche Routen er zu reagieren hat


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

### Routing

Durch das Routing verknüpfst du bestimmte Request mit von dir erstellten Funktionen, die einen String zurückgeben und dadurch an den Client (in den meisten Fällen wird dies wohl einen Browser sein) gesendet werden.

Möchtest du bspw. das nach dem Aufruf von `index.php/hello` ein freundliches `Hallo!` ausgegeben wird, dann lässt sich das wie folgt lösen.

```php
$app->get('/hello', function () {
    return 'Hallo!';
});
```

Du hast somit die Route `/hello` registriert. Wird also im Browser dieses Route aufgerufen, dann führt Hans den Callback aus, der wiederum die freundliche Begrüßung zurückgibt. Das Ergebnis sieht also wie folgt aus:

```
Hallo!
```

In diesem Fall wird allerdings nur auf GET-Requests reagiert. Möchtest du, dass nur auf POST-Requests reagiert wird, dann nutze die `post()`-Methode, soll nur auf DELETE-Request reagiert werden, dann nutze `delete()` usw.

### Parameter für Routing-Callbacks

Du kannst beliebige Parameter für den Callback einer Route benutzen - *Hahns* setzt diese automatisch. Dabei schaut er bevor der Callback ausgeführt wird welche Parameter erwartet werden und setzt diese dann entsprechend. Es ist also zwingend erforderlich, dass die Parameter typisiert sind.

Es können nun Parameter benutzt werden, die vorher dafür registriert (siehe weiter unten) wurden. Bereits vorregistriert sind:

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

#### Erstelle deinen eigenen Route-Parameter

Bis darauf, dass ein Parameter ein Objekt sein muss, sind keine besonderen Bedingungen an einen Parameter geknüpft. Du kannst beliebige neue Typen mit der `parameter()`-Methode registrieren. Diese erwartet als erstes Argument den Typen des zu registrierenden Parameters und als zweites Argument einen Callback in dem das Objekt instanziiert und zurückgegeben wird.

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

Optional kannst du auch die Instanz von `\Hahns\Hahns` in deinem Callback nutzen. Dazu reicht es einfach einen entsprechenden Parameter zu setzen.

```php
$app->parameter('\\stdClass', function(\Hahns\Hahns $app) {
    // ...
});
```

Parameter werden per Default als Singleton gehandhabt. Das heißt, dass der Callback nur einmalig aufgerufen wird, das zurückgegebe Objekt wird gespeichert und im weiteren wiederverwendet. Möchtest du allerdings, dass der Callback bei jeder Benutzung erneut aufgerufen wird (der Parameter also jedes Mal erneut erstellt wird), dann übergeben der `parameter
()`-Methode als drittes Argument `false`

```php
$app->parameter('\\stdClass' function() {
    // ....
}, false);
```


### Named Parameter

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

Services sind benannte Objekte, die einmalig erstellt werden und über *Hahns* jederzeit verfügbar sind. Um auf einen Service zuzugreifen nutzt du die `service()`-Methode von *Hahns*.

```php
$app->service('service-name');
```

Per Default sind folgende Services verfügbar:

* `html-response` liefer eine Instanz von  `\Hahns\Response\Html`
* `json-response` liefer eine Instanz von  `\Hahns\Response\Json`
* `text-response` liefer eine Instanz von  `\Hahns\Response\Text`


#### Erstelle deinen eigenen Service

Services sind optimal dazu geeignet andere Libraries innerhalb deiner Application zu nutzen - also bspw. [Twig](http://twig.sensiolabs.org/) als Template Enginge oder [Propel](http://propelorm.org/) als ORM.

Um einen Service zu erstellen nutzt du ebenfalls die `service()`-Methode von Hahns

```php
$app->service('myservice', function() {
    $service = new \stdClass();
    $service->test = 'hello';
    return $service;
});
```

Als erstes Argument gibst du den Namen an mit dem du auf den Service später zugreifen möchtest. Als zweiten Parameter übergibst du einen Callback, der den zu nutzenden Service erstellt und zurückgibt.

Das besondere an Services sind, dass der Callback in jedem Fall nur ein Mal ausgeführt wird. Hier kannst du alos sämtliche Konfiguration u.Ä. auführen ohne dir Sorgen machen zu müssen, dass diese mehrfach durchgeführt wird.

Optional kannst du auch die Instanz von `\Hahns\Hahns` in deinem Callback nutzen. Dazu reicht es einfach einen entsprechenden Parameter zu setzen.

```php
$app->service('myservice', function(\Hahns\Hahns $app) {
    // ...
});
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
