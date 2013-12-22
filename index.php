<?php

require 'vendor/autoload.php';

$r = new \Hahns\Router();

$r->get('/', function() {
    echo 'hello home!';
});

$r->get('/hallo', function() {
    echo 'hello welt!';
});

$r->get('/hello', function() {
    echo 'hello world!';
});

$r->get('/hello/[.*:name]', function($args) {
    printf('hello %s', $args['name']);
});

$r->get('/hallo/[.*:name]', function($args) {
    printf('hallo %s', $args['name']);
});

$r->get('/hello/[.+:first]/[\d+:second]', function($args) {
    printf('hello %s-%s', $args['first'], $args['second']);
});

$r->get('/hallo/[.+:first]/[\d+:second]', function($args) {
    printf('hallo %s-%s', $args['first'], $args['second']);
});

$r->get('/hello/[.*:first]/[.*:second]', function($args) {
    printf('hello %s and %s', $args['first'], $args['second']);
});

$r->get('/hallo/[.*:first]/[.*:second]', function($args) {
    printf('hallo %s und %s', $args['first'], $args['second']);
});


$r->dispatch();
