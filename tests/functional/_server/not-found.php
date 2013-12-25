<?php

/* @var \Hahns\Hahns $app */

$app->on(\Hahns\Hahns::EVENT_BEFORE_ROUTING, function($usedRoute, \Hahns\Hahns $app) {
    $app->config('route', $usedRoute);
});


$app->get('/not-found', function (\Hahns\Response\Html $response) {
    $response->header('test0r', 'yep');
    throw new \Hahns\Exception\Http\NotFoundException();
});

