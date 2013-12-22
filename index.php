<?php

require 'vendor/autoload.php';

$app = new \Hahns\Hahns();

$app->get('/', function () {
    echo 'hello home!';
});

$app->get('/hallo', function () {
    echo 'hello welt!';
});

$app->get('/hello', function () {
    echo 'hello world!';
});

$app->get('/hello/[.*:name]', function ($args) {
    printf('hello %s', $args['name']);
});

$app->get('/hallo/[.*:name]', function ($args) {
    printf('hallo %s', $args['name']);
});

$app->get('/hello/[.+:first]/[\d+:second]', function ($args) {
    printf('hello %s-%s', $args['first'], $args['second']);
});

$app->get('/hallo/[.+:first]/[\d+:second]', function ($args) {
    printf('hallo %s-%s', $args['first'], $args['second']);
});

$app->get('/hello/[.*:first]/[.*:second]', function ($args) {
    printf('hello %s and %s', $args['first'], $args['second']);
});

$app->get('/hallo/[.*:first]/[.*:second]', function ($args) {
    printf('hallo %s und %s', $args['first'], $args['second']);
});


$app->run();
