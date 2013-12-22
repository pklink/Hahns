<?php

require 'vendor/autoload.php';

$app = new \Hahns\Hahns();

$app->get('/', function () {
    return 'hello home!';
});

$app->get('/hallo', function () {
    return 'hello welt!';
});

$app->get('/hello', function () {
    return 'hello world!';
});

$app->get('/hello/[.*:name]', function (\Hahns\Request $request, \Hahns\Response\JsonImpl $response) {
    return $response->send(['hello' => $request->get('name')]);
});

$app->get('/hallo/[.*:name]', function (\Hahns\Response\JsonImpl $response, \Hahns\Request $request) {
    return $response->send(['hallo' => $request->get('name')]);
});

$app->get('/hello/[.+:first]/[\d+:second]', function (\Hahns\Request $request) {
    return sprintf('hello %s-%s', $request->get('first'), $request->get('second'));
});

$app->get('/hallo/[.+:first]/[\d+:second]', function (\Hahns\Request $request) {
    return sprintf('hallo %s-%s', $request->get('first'), $request->get('second'));
});

$app->get('/hello/[.*:first]/[.*:second]', function (\Hahns\Request $request) {
    return sprintf('hello %s and %s', $request->get('first'), $request->get('second'));
});

$app->get('/hallo/[.*:first]/[.*:second]', function (\Hahns\Request $request) {
    return sprintf('hallo %s und %s', $request->get('first'), $request->get('second'));
});


$app->run();
