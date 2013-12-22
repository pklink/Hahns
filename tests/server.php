<?php

require '../vendor/autoload.php';

$app = new \Hahns\Hahns();

$app->get('/', function (\Hahns\Response\JsonImpl $response) {
    return $response->send(['message' => 'home']);
});

$app->get('/hallo', function (\Hahns\Response\JsonImpl $response) {
    return $response->send(['message' => 'hallo welt!']);
});

$app->get('/hello', function (\Hahns\Response\JsonImpl $response) {
    return $response->send(['message' => 'hello world!']);
});

$app->get('/hello/[.*:name]', function (\Hahns\Request $request, \Hahns\Response\JsonImpl $response) {
    return $response->send([
        'message' => sprintf('hello %s', $request->get('name'))
    ]);
});

$app->get('/hallo/[.*:name]', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send([
        'message' => sprintf('hallo %s', $request->get('name'))
    ]);
});

$app->get('/hello/[.+:first]/[\d+:second]', function (\Hahns\Request $request, \Hahns\Response\JsonImpl $response) {
    return $response->send([
        'message' => sprintf('hello %s-%d', $request->get('first'), $request->get('second'))
    ]);
});

$app->get('/hallo/[.+:first]/[\d+:second]', function (\Hahns\Request $request, \Hahns\Response\JsonImpl $response) {
    return $response->send([
        'message' => sprintf('hallo %s-%d', $request->get('first'), $request->get('second'))
    ]);
});

$app->get('/hello/[.*:first]/[.*:second]', function (\Hahns\Request $request, \Hahns\Response\JsonImpl $response) {
    return $response->send([
        'message' => sprintf('hello %s %s', $request->get('first'), $request->get('second'))
    ]);
});

$app->get('/hallo/[.*:first]/[.*:second]', function (\Hahns\Request $request, \Hahns\Response\JsonImpl $response) {
    return $response->send([
        'message' => sprintf('hallo %s %s', $request->get('first'), $request->get('second'))
    ]);
});


$app->run();
