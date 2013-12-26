<?php

/* @var \Hahns\Hahns $app */

$app->get('/route1', function (\Hahns\Response\Text $response) {
    return $response->send('hello world');
}, 'route1');
$app->get('/route2', 'route1', 'route2');
$app->get('/route3', 'route2');