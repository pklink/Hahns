<?php

require 'vendor/autoload.php';

$r = new \Hahns\Router();

$r->get('/hello', function() {
    echo 'hello world!';
});

$r->get('/hello/[.*:name]', function($args) {
    printf('hello %s', $args['name']);
});

$r->get('/hello/[.+:first]/[\d+:second]', function($args) {
    printf('hello %s-%s', $args['first'], $args['second']);
});

$r->get('/hello/[.*:first]/[.*:second]', function($args) {
    printf('hello %s and %s', $args['first'], $args['second']);
});


$r->dispatch();
