<?php

/* @var \Hahns\Hahns $app */

$app->on(\Hahns\Hahns::EVENT_BEFORE_ROUTING, function($usedRoute, \Hahns\Hahns $app) {
    $app->config('route', $usedRoute);
});


$app->get('/event', function (\Hahns\Hahns $app) {
    echo $app->config('route');
});

